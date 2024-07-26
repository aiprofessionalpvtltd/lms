<script src="{{asset('backend/js/app.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var urlPath = '<?php echo e(url("")); ?>';
    var CSRF_TOKEN = '<?php echo e(csrf_token()); ?>';
    {{--var getProvinceByCountry = '<?php echo e(url('get-province-by-country')); ?>';--}}
    {{--var getDistrictByProvince = '<?php echo e(url('get-district-by-province')); ?>';--}}
    {{--var getCityByProvince = '<?php echo e(url('get-city-by-province')); ?>';--}}
    {{--var pdfIcon = '<?php echo e(asset('assets/pdf-icon.png')); ?>';--}}
</script>

@stack('script')
