<?php
$name ="jefy okta";
$script = "<script>const name = 'jefyokta'</script>";
function getUmur(){
    return 23;
}


?>

<h1> nama :<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h1>
<h1> nama :<?php echo htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?></h1>
<h1> nama :<?php echo htmlspecialchars(getUmur(), ENT_QUOTES, 'UTF-8'); ?></h1>
