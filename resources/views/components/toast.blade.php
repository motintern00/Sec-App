@if(session('success'))
    <span data-flash-success class="d-none">{{ session('success') }}</span>
@endif
@if(session('error'))
    <span data-flash-error class="d-none">{{ session('error') }}</span>
@endif
<div class="toast-container position-fixed top-0 end-0 p-3" id="gpa-toast-container" style="z-index: 11000;"></div>
