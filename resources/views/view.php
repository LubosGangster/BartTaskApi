<html>
<body>
<h1>Wait for minute.</h1>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        console.log(document.URL.replace(/ajax\/|#/g, ''));
        document.location = document.URL.replace(/ajax\/|#/g, '');
    });
</script>
