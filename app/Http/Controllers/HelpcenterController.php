<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpCenterCategory;
use App\Models\HelpCenterTopic;
use Illuminate\Support\Facades\Log;

class HelpcenterController extends Controller
{
    public function index($category = null, $topic = null)
    {
        if ($category) {$category = HelpCenterCategory::whereRaw("replace(lower(category),' ','-') = '$category'")->firstOrFail();
        Log::debug('help->  '.json_encode($category) );
        }
        if ($topic) {
        
            $topic = HelpCenterTopic::where("category", $category->category)->where("slug", $topic)->firstOrFail();
            $allTopics = HelpCenterTopic::where("category", $category->category)->pluck('name');
        }

        // return view('pages.helpcenter.index', compact("category", "topic", "allTopics"));
        return view('pages.helpcenter.index', compact("category", "topic"));
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $categories = HelpCenterCategory::with("topics");

        if (isset($data["filter"])) {
            $filter = $data["filter"];
            $ids = HelpCenterTopic::where("name", "like", "%" . $filter . "%")->get()->pluck("id")->toArray();
            $categories = $categories->whereIn("id", $ids);
        }
        $categories = $categories->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }
}
