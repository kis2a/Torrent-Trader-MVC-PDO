<?php
// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="M-jay" />
    <meta name="generator" content="TorrentTrader <?php echo VERSION; ?>" />
    <meta name="description" content="TorrentTrader is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker. Featuring intergrated forums, and plenty of administration options. Please visit www.torrenttrader.xyx for the support forums. " />
    <meta name="keywords" content="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" />
    <title><?php echo $title; ?></title>
  
    <!-- Bootstrap & core CSS -->
    <link href="<?php echo URLROOT; ?>/assets/themes/<?php echo ($_SESSION['stylesheet'] ?: Config::TT()['DEFAULTTHEME']) ?>/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URLROOT; ?>/assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- TT Custom CSS, any edits must go here-->
    <link href="<?php echo URLROOT; ?>/assets/themes/<?php echo ($_SESSION['stylesheet'] ?: Config::TT()['DEFAULTTHEME']) ?>/customstyle.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/monokai-sublime.min.css">
  </head>
<body>

<?php require "assets/themes/default/navbar.php"; ?>

<div class="container-fluid">
<div class="row page">
  
<?php
if (Config::TT()['LEFTNAV']) { ?>
<div class="col ttsidebar">
  <?php Blocks::left();?>
</div> <?php
} ?>

<div class="col">

<th style="width: 70%">