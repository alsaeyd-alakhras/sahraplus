@if (session()->has($type))
    <script>
        const type = "{{ $type }}";
        const messages = "{{ session($type) }}";
        if(type == 'success'){
            toastr.success(messages);
        }else if(type == 'warning'){
            toastr.warning(messages);
        }else if(type == 'error' || type == 'danger'){
            toastr.error(messages);
        }else{
            toastr.info(messages);
        }
    </script>
@endif
