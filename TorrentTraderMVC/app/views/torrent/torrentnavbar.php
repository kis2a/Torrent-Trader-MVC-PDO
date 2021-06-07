<a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Back</button></a>
<a href='<?php echo URLROOT; ?>/torrent/edit?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Edit</button></a>
<a href='<?php echo URLROOT; ?>/comments?type=torrent&amp;id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Comments</button></a>
<a href='<?php echo URLROOT; ?>/torrent/torrentfilelist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Files</button></a>

<?php if ($row["external"] != 'yes') {?>
     <a href='<?php echo URLROOT; ?>/peers/peerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Peers</button></a>
<?php }?>

<?php if ($row["external"] == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrent/torrenttrackerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm btn-warning">Trackers</button></a>
<?php } ?>
<br><br>