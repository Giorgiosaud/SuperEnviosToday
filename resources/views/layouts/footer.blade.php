<script>
   window.Laravel = {!! json_encode([
       'csrfToken' => csrf_token(),
       'apiToken' => $currentUser->api_token ?? null,
   ]) !!};
</script>