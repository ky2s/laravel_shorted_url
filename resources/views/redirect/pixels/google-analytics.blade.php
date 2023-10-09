<script async src="https://www.googletagmanager.com/gtag/js?id={{ $pixel->pixel_id }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{ $pixel->pixel_id }}');
</script>