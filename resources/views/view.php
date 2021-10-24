<html>
<body>
    <div id="result">

    </div>
</body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        try {
            console.log(window.location.hash.match(/#(?:access_token)=([\S\s]*?)&/)[1]);
            if (window.location.hash !== '' &&
                window.location.hash.match(/#(?:access_token)=([\S\s]*?)&/) != null &&
                window.location.hash.match(/#(?:access_token)=([\S\s]*?)&/)[1] != null)
            {
                let hash = window.location.hash.substr(1);
                document.location = window.location.origin + '/api/callback/ajax?' + hash;
            } else {
                document.location = window.location.origin + '/api/callback/ajax?error=you were not redirected from facebook';
            }
        } catch (e) {
            document.location = window.location.origin + '/api/callback/ajax?error=you were not redirected from facebook';
        }

        // $.ajax({
        //     type: 'GET',
        //     url: '/api/callback/ajax?'+hash,
        // }).done(function (data) {
        //     console.log("well done")
        // }).error(function () {
        //     console.log("internal server error")
        // });
        // access_token = hash.match(/#(?:access_token)=([\S\s]*?)&/)[1];
        // let access_token = '';
        // for (let i = 0; i < hash.length; i++) {
        //     if (hash.charAt(i) === '='){
        //         for (let j = i + 1; j < hash.length; j++) {
        //             if (hash.charAt(j) === '&'){
        //                 break;
        //             }
        //             access_token += hash.charAt(j)
        //         }
        //         break;
        //     }
        // }
        //
        // $('#result').html(JSON.stringify({
        //     "token": access_token
        // }));
    });
</script>
