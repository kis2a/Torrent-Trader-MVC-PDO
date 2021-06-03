<?php
if ($_SESSION['loggedin'] === true && $_SESSION["control_panel"] == "yes") {
    Block::begin(Lang::T("AdminCP"));
    ?>
        <!-- content -->
       <select name="admin" style="width: 95%" onchange="if(this.options[this.selectedIndex].value != -1){ window.location = this.options[this.selectedIndex].value; }">
       <option value="-1">Navigation</option>
       <option value="<?php echo URLROOT; ?>/adminadvancedsearch">Advanced User Search</option>
       <option value="<?php echo URLROOT; ?>/adminavatar">Avatar Log</option>
       <option value="<?php echo URLROOT; ?>/adminbackup">Backups</option>
       <option value="<?php echo URLROOT; ?>/adminipban">Banned Ip's</option>
       <option value="<?php echo URLROOT; ?>/adminbantorrent">Banned Torrents</option>
       <option value="<?php echo URLROOT; ?>/admincp/blocks&amp;do=view">Blocks</option>
       <option value="<?php echo URLROOT; ?>/admincensor/cheats">Detect Possibe Cheats</option>
       <option value="<?php echo URLROOT; ?>/adminemailban">E-mail Bans</option>
       <option value="<?php echo URLROOT; ?>/adminfaq/manage">FAQ</option>
       <option value="<?php echo URLROOT; ?>/adminfreetorrent">Freeleech Torrents</option>
       <option value="<?php echo URLROOT; ?>/admincomments">Latest Comments</option>
       <option value="<?php echo URLROOT; ?>/adminmessages/mass">Mass PM</option>
       <option value="<?php echo URLROOT; ?>/adminmessages">Message Spy</option>
       <option value="<?php echo URLROOT; ?>/admincp/news&amp;do=view">News</option>
       <option value="<?php echo URLROOT; ?>/adminpeers">Peers List</option>
       <option value="<?php echo URLROOT; ?>/admincp/polls&amp;do=view">Polls</option>
       <option value="<?php echo URLROOT; ?>/adminreports&amp;do=view">Reports System</option>
       <option value="<?php echo URLROOT; ?>/admincp/rules&amp;do=view">Rules</option>
       <option value="<?php echo URLROOT; ?>/adminlog">Site Log</option>
       <option value="<?php echo URLROOT; ?>/teams/create">Teams</option>
       <option value="<?php echo URLROOT; ?>/adminstylesheet">Theme Management</option>
       <option value="<?php echo URLROOT; ?>/admincp/categories&amp;do=view">Torrent Categories</option>
       <option value="<?php echo URLROOT; ?>/admincp/torrentlangs&amp;do=view">Torrent Languages</option>
       <option value="<?php echo URLROOT; ?>/admintorrents">Torrents</option>
       <option value="<?php echo URLROOT; ?>/admincp/groups&amp;do=view">Usergroups View</option>
       <option value="<?php echo URLROOT; ?>/adminwarning">Warned Users</option>
       <option value="<?php echo URLROOT; ?>/adminwhoswhere">Who's Where</option>
       <option value="<?php echo URLROOT; ?>/admincensor">Word Censor</option>
       <option value="<?php echo URLROOT; ?>/adminforum">Forum Management</option>
       </select>
    <!-- end content -->

<?php block::end();
}