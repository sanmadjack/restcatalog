<!DOCTYPE html>
<html>
<head>
<title>Catalog</title>
<!-- TODO: change this to dynamically change to the compressed version when not in a dev environment, maybe add the pre-IE 9 version as well -->
<script type="text/javascript" src="libs/jquery/jquery-2.1.0.js"></script>
<script type="text/javascript" src="libs/rest/RestController.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var rest = new RestController("http://restcatalog-c9-sanmadjack.c9.io/api/");
        rest.sendRequest("GET","nuke");
    
    });
</script>
</head>
<body>
sup
</body>
</html>