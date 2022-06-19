@if(session('quick.alert'))
    <div class="alert alert-{{ session('quick.alert.type') }} alert-dismissible fade show custom-alert-shadow" role="alert">
        <div class="d-flex">
            <div class="p-2">
                @if((session('quick.alert.type')=="warning")||(session('quick.alert.type')=="danger"))
                    <i class="material-icons">warning</i>
                @elseif((session('quick.alert.type')=="success"))
                    <i class="material-icons">check_circle</i>
                @elseif((session('quick.alert.type')=="info"))
                    <i class="material-icons">info</i>
                @endif
            </div>
            <div class="p-2">{!! session('quick.alert.message') !!}</div>
            <div class="p-2">
                @if(session('quick.alert.closeable'))
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="top: 13px;">
                        <span aria-hidden="true">×</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
    <?php Session(["quick"=>null]); ?>
@endif