<?php
$tcat = implode(',', PopularCats);
$i = 0;
$q = "SELECT torrents.id, torrents.image1, torrents.seeders, torrents.leechers, torrents.added FROM torrents WHERE visible = 'yes' AND banned = 'no' AND image1 != '' AND category IN ($tcat) ORDER BY torrents.seeders + torrents.leechers DESC LIMIT 10"; 
$q = DB::run($q);

Style::block_begin('Popular Movies');
print '<table>';
print '<tr>';
while ( $r = $q->fetch(PDO::FETCH_ASSOC) )
{
	print (($i && $i % 2) ? '
	<td><a href="'.URLROOT.'/torrent?id='.$r['id'].'">
	<img src="' . data_uri(UPLOADDIR."/images/".$r["image1"], $r['image1']) . '" height="100" width="100" border="0" /></a></td>
	</tr>' : '
	<tr><td><a href="'.URLROOT.'/torrent?id='.$r['id'].'">
	<img src="' . data_uri(UPLOADDIR."/images/".$r["image1"], $r['image1']) . '" height="100" width="80" border="0" /></a></td>');
	$i++;
} 
print '</tr>';
print '</table>';
Style::block_end();