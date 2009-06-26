<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Wikir | <?= empty($page_name) ? 'Limonade PHP example' : $page_name;?></title>
	<meta name="generator" content="TextMate http://macromates.com/">
	<meta name="author" content="Fabrice Luraine">
	<link rel="stylesheet" href="./public/blueprint/screen.css" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="./public/blueprint/print.css" type="text/css" media="print"> 
  <!--[if IE]>
    <link rel="stylesheet" href="./public/blueprint/ie.css" type="text/css" media="screen, projection">
  <![endif]-->

  <link rel="stylesheet" href="./public/screen.css" type="text/css" media="screen, projection">

	<!-- Date: 2009-06-03 -->
</head>
<body>
<div id="header">
  <h1><a href="./">Wikir</a></h1>
  <p>Limonade PHP Example (with raspberry syrup)</p>
</div>
  <div id="content">
    <?= $content; ?>
    <p><em><small>(Note: a reset of all wiki contents is sheduled once a day)</small></em></p>
  </div>
  <div id="pages_cloud">
    <h5>Pages Cloud</h5>
    <p><?=html_pages_cloud(); ?></p>
    
  </div>
  <div id="footer">
		<p>© 2009 Fabrice Luraine, Mathias Standaert | Aerated with <a href="http://sofa-design.net/limonade/" title="Limonade PHP micro-framework">Limonade</a></p>
	</div>
</body>
</html>
