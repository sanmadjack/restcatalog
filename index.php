<!DOCTYPE html>
<html>
<head>
<title>Catalog</title>

<link rel="stylesheet" type="text/css" href="themes/holo/css/theme.css">


<!-- TODO: change this to dynamically change to the compressed version when not in a dev environment, maybe add the pre-IE 9 version as well -->
<script type="text/javascript" src="libs/js/jquery/jquery-2.1.0.js"></script>
<script type="text/javascript" src="libs/js/debug/Debug.js"></script>
<script type="text/javascript" src="libs/js/rest/RestController.js"></script>
<script type="text/javascript" src="libs/js/model/Model.js"></script>
<script type="text/javascript" src="libs/js/widgets/"></script>
<script type="text/javascript">
    var model = null;
    var debug = null;
    
    $(document).ready(function(){
        debug = new Debug();
        model = new Model("api/");
    });
    
    function NukeIt() {
        model.nuke();
    }
    
    function GoBack() {
        // Do something?
    }
</script>
<style type="text/css">
div {

}
</style>

</head>
<body>

<div id="title">Catalog <input type="button" value="Nuke 'Em!" onclick="NukeIt();"/></div>

<div id="back"><input type="image" src="themes/holo/images/back.png" alt="Back" title="Black" onclick="GoBack();" /></div>

<div id="menu">
SanMadJack
<div class="menu_item">Settings</div>
<div class="menu_item">Settings</div>
</div>

<div id="search">
    <input type="text" id="search_input" />
</div>

<div id="content"></div>

</body>
</html>