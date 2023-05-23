<div class="modal fade" id="logoutModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="logoutModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="logoutModalTitle"><i class="fa fa-sign-out"></i> Log <strong>Out</strong> ?</h5>
        </div>
        <div class="modal-body">
            <p> {{ __('Are you sure you want to log out?') }}</p>
            <p> {{ __('Press No if you want to continue work. Press Yes to logout current user.') }}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
            <a class="btn btn-success" href="javascript:;" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <i class="fas fa-check"></i> {{ __('Yes') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

        </div>
        </div>
    </div>
</div>
