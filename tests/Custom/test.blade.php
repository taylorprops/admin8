
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8=" crossorigin="anonymous"></script>

    <style>
        #target_div {
            width: 500px;
            height: 400px;
            margin: 100px;
        }
    </style>
</head>
<body>

    <div id="target_div"></div>

    <script>

        $(document).ready(function() {
            load_html();
        });

        function load_html() {
            $.ajax({
                type: 'GET',
                url: 'http://TimDemo1.gojuniata.com/ref/academicsegment1.html',
                success: function(response) {
                    $('#target_div').html(response);
                }
            });
        }

    </script>
</body>
</html>
