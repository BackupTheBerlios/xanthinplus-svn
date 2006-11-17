<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php print $data->m_title ?><title>
<META name="description" content="<?php print $data->m_description ?>">
<META name="keywords" content="<?php print implode(',',$data->m_keywords) ?>">
<META name="Content-language" content="<?php print $data->m_language ?>">
<?php foreach($data->m_stylesheets as $style):?>
<style type="text/css" media="all">@import "<?php print $style ?>"</style>
<?php endforeach; ?>
</head>
<body>
<div id="content">
<?php $data->m_components['content']->display() ?>
</div>
</body>
</html>