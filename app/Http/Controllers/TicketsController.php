<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Datatable\Datatable;
use Carbon\Carbon;
use App\Http\Requests\createTicketRequest;
use App\Http\Requests\createTicketCommentRequest;
use App\Notifications\Tickets\NewTicket;
use App\Services\AlertService;
use Auth;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        $filter = ["filter" => $request->all()];
        $status = DB::connection("dashboard_server")->table("ticketit_statuses")->get();
        $categories = DB::connection("dashboard_server")->table("ticketit_categories")->get();
        $priorities = DB::connection("dashboard_server")->table("ticketit_priorities")->get();
        return view("pages.tickets.index", compact("filter", "categories", "status", "priorities"));
    }

    public function create()
    {
        $categories = DB::connection("dashboard_server")->table("ticketit_categories")->get();
        return view("pages.tickets.create", compact("categories"));
    }

    public function store(createTicketRequest $request)
    {
        $data = $this->getInitialValues();
        $data["subject"] = $request["subject"];
        $data["html"] = $request["html"];
        $data["category_id"] = $request["category_id"];
        $data["content"] = strip_tags(str_replace("</p>", "</p>\n", $request["html"]));
        $data['id'] = DB::connection("dashboard_server")->table("ticketit")->insertGetId($data);

        AlertService::flash('success', "Ticket cadastrado com sucesso !!");
        $user = \Auth::user();

        if (config("app.env") == "production") {
            $user->notify(new NewTicket($data));
        }

        return redirect(route('tickets.index'));
    }

    public function new_comment($id, createTicketCommentRequest $request)
    {
        $comment = $request->only("comment")["comment"];
        $date =  Carbon::now()->toDateTimeString();
        $data = [
            "content" => strip_tags($comment),
            "html" => $comment,
            "ticket_id" => $id,
            "user_id" => Auth::user()->id,
            "created_at" => $date,
            "updated_at" => $date,
        ];
        DB::connection("dashboard_server")->table("ticketit_comments")->insert($data);
        AlertService::flash('success', "Comentário adicionado com sucesso !!");
        return redirect(route('tickets.show', ["id" => $id]));
    }

    public function show($id)
    {
        $user_id = Auth::user()->id;
        $ticket = DB::connection("dashboard_server")->table("ticketit")
            ->join("ticketit_priorities as ticketit_priorities", "ticketit_priorities.id", "=", "ticketit.priority_id")
            ->join("ticketit_categories as ticketit_categories", "ticketit_categories.id", "=", "ticketit.category_id")
            ->join("ticketit_statuses as ticketit_statuses", "ticketit_statuses.id", "=", "ticketit.status_id")
            ->select(["ticketit.*", "ticketit_priorities.name as priority_name", "ticketit_categories.name as category_name", "ticketit_statuses.name as status_name"])
            ->where("ticketit.user_id", $user_id)
            ->where("ticketit.id", $id)
            ->first();
        if (!$ticket)
            abort(404);
        $comments = DB::connection("dashboard_server")->table("ticketit_comments")
            ->join("users as users", "users.id", "=", "ticketit_comments.user_id")
            ->where("ticket_id", $ticket->id)
            ->select(["ticketit_comments.*", "users.name as user_name"])
            ->get();

        return view("pages.tickets.show", compact("ticket", "comments", "user_id"));
    }

    public function search(Request $request)
    {
        $query = DB::connection("dashboard_server")->table("ticketit")->where("ticketit.id", ">", 0)
            ->join("ticketit_priorities as ticketit_priorities", "ticketit_priorities.id", "=", "ticketit.priority_id")
            ->join("ticketit_categories as ticketit_categories", "ticketit_categories.id", "=", "ticketit.category_id")
            ->join("ticketit_statuses as ticketit_statuses", "ticketit_statuses.id", "=", "ticketit.status_id")
            ->select(["ticketit.*", "ticketit_priorities.name as priority_name", "ticketit_categories.name as category_name", "ticketit_statuses.name as status_name"])
            ->where("ticketit.user_id", Auth::user()->id);
        $datatable = new Datatable($query);
        $datatable->setColumnsOrder(["ticketit.id", "ticketit.subject", "ticketit.updated_at", "ticketit.status_id", "ticketit.priority_id", "ticketit.category_id"]);
        $callback = function ($row) {
            $detail_route = route("tickets.show", ["id" => $row->id]);
            $data = [
                "<a class='link' href='$detail_route'>#" . str_pad($row->id, 6, "0", STR_PAD_LEFT) . "</a>",
                $row->subject,
                Carbon::createFromDate($row->updated_at)->diffForHumans(),
                $row->completed_at ? "Finalizado" : $row->status_name,
                $row->priority_name,
                $row->category_name,
                "<a href='$detail_route'><b><span class='d-flex align-items-center float-right'>Ver Detalhes<i class='ml-2 material-icons'>more_vert</i></span></b></a>",
            ];
            return $data;
        };
        $filterLogic = function ($results, $filters) {
            if (@$filters["code"]) {
                $code = $filters["code"];
                $code = trim($code);
                $results = $results->where("ticketit.id", "like", "%$code%");
            }
            if (@$filters["subject"]) {
                $subject = $filters["subject"];
                $results = $results->where("ticketit.subject", "like", "%$subject%");
            }
            if (@$filters["status"]) {
                $status = $filters["status"];
                if (($status != "all") && ($status != "completed"))
                    $results = $results->where("ticketit.status_id", $status)->where("ticketit.completed_at", null);
                if ($status == "completed")
                    $results = $results->where("ticketit.completed_at", "!=", null);
            }
            if (@$filters["priority"]) {
                $priority = $filters["priority"];
                if ($priority != "all")
                    $results = $results->where("ticketit.priority_id", $priority);
            }
            if (@$filters["category"]) {
                $category = $filters["category"];
                if ($category != "all")
                    $results = $results->where("ticketit.category_id", $category);
            }
            return $results;
        };
        return $datatable->make($request, $callback, $filterLogic);
    }

    private function getInitialValues()
    {
        $status = DB::connection("dashboard_server")->table("ticketit_statuses")->first();
        $priority = DB::connection("dashboard_server")->table("ticketit_priorities")->first();
        $date = Carbon::now()->toDateTimeString();
        $user_id =  Auth::user()->id;
        $values = [
            "status_id"   =>  @$status->id,
            "priority_id" =>  @$priority->id,
            "created_at"  =>  $date,
            "updated_at"  =>  $date,
            "user_id"     =>  $user_id,
            "agent_id"    =>  $user_id,
        ];
        return $values;
    }
}
