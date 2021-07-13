<a href='<?php echo URLROOT; ?>/torrent?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Back</button></a>
<a href='<?php echo URLROOT; ?>/torrent/edit?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Edit</button></a>
<a href='<?php echo URLROOT; ?>/comments?type=torrent&amp;id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Comments</button></a>
<a href='<?php echo URLROOT; ?>/torrent/torrentfilelist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Files</button></a>

<?php if ($row["external"] != 'yes') {?>
     <a href='<?php echo URLROOT; ?>/peers/peerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Peers</button></a>
<?php }?>

<?php if ($row["external"] == 'yes') {?>
     <a href='<?php echo URLROOT; ?>/torrent/torrenttrackerlist?id=<?php echo $data['id']; ?>'><button type="button" class="btn btn-sm ttbtn">Trackers</button></a>
<?php } ?>
<br><br>