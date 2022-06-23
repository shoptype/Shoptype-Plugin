<?php

if( isset($_POST['id']) && isset($_POST['name'])  ){
    
    global $wpdb;
     $to      = $_POST['mail'];
     $subject = $_POST['name'].'invited for coseller'; 
     $message = $_POST['name'].'you invited for coseller<br>Ref id is'.$_POST['refcode']; 
     $headers = "From: ".$_POST['nam‌​e​']." <".$_POST['m‌​ail‌​'].">\r\n"; $headers = "Reply-To: ".$_POST['ma‌​il‌​']."\r\n"; 
     $headers = "Content-type: text/html; charset=iso-8859-1\r\n";
     'X-Mailer: PHP/' . phpversion();
     if(mail($to, $subject, $message, $headers));
     $table_name=$wpdb->prefix.'usermeta'; 
     $user_id=$_POST['id'];
     $user_role='a:2:{s:10:"subscriber";b:1;s:8:"coseller";b:1;}';
     
     $post_id =$wpdb->query($wpdb->prepare("UPDATE wp_users SET display_name = %s WHERE ID = %s;",$user_role,$user_id));
     

    //$query='UPDATE '.$table_name.' SET meta_value="s:10:"subscriber";b:1;s:8:"coseller" WHERE userid= '.$user_id.' AND meta_key="wp_capabilities"';
     $post_id =$wpdb->query($wpdb->prepare("UPDATE wp_usermeta SET meta_value= %s WHERE user_id= %s AND meta_key='wp_capabilities'",$user_role,$user_id));
     //$post_id = $wpdb->get_results("SELECT meta_value FROM wp_usermeta WHERE (meta_key = 'wp_capabilities' AND userid = '2')");
     //$post_id = $wpdb->get_results("");
     //print_r($query);
     print_r($post_id); 
     unset($_POST);
     exit;
   }
function coselluserlist($stRefcode)
{?>
    <ul id="ulfriends">
    
 <?php 

    $no=120;// total no of author to display

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if($paged==1){
      $offset=0;  
    }else {
       $offset= ($paged-1)*$no;
    }


 $user_query = new WP_User_Query( array('number' => $no, 'offset' => $offset ) );
       if ( ! empty( $user_query->results ) ) {
        ?>
        <div id="inviteall" class="page-title-action" onclick="sendInviteAll()" style="width: fit-content;">Invite All</div>
        <table class="wp-list-table widefat fixed striped table-view-list users">
    <thead>
    <tr>
    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc"><a href=""><span>Username</span><span class="sorting-indicator"></span></a></th><th scope="col" id="name" class="manage-column column-name">Name</th><th scope="col" id="email" class="manage-column column-email sortable desc"><a href="http://localhost/mayko/wp-admin/users.php?orderby=email&amp;order=asc"><span>Email</span><span class="sorting-indicator"></span></a></th><th scope="col" id="role" class="manage-column column-role">Role</th><th scope="col" id="posts" class="manage-column column-posts num">invite</th>  </tr>
    </thead>

    <tbody id="the-list" data-wp-lists="list:user">
    <?php foreach ( $user_query->results as $user ) {
        $user = get_userdata( $user->ID );
        
        // Get all the user roles as an array.
        $user_roles = $user->roles;
        if ( in_array( 'coseller', (array) $user->roles ) ) {
            $invitedtext='success';
        }
        else
        {
            $invitedtext='invite';
        }
        $user_roles = implode(', ', $user_roles);
        if(!(in_array( 'administrator', (array) $user->roles )))
        {
        ?>
        <form action method="post">
         <tr id="user- <?php echo $user->ID ?> ">
         <th scope="row" class="check-column"><label class="screen-reader-text" for="user_1"></label><input type="checkbox" name="users[]" id="int- <?php echo $user->ID ?>" data="<?php echo $user->ID ?>" class="inviteallcheck" value="1"></th>
         <td class="username column-username has-row-actions column-primary" data-colname="Username">
            <?php echo get_avatar( $user->user_email, 32 ) ?><strong><a href="">
                <?php echo $user->user_nicename ?></a></strong>
                <td class="name column-name" data-colname="Name">
                    <span aria-hidden="true"><?php echo  $user->first_name ?>
                </span></td><td class="email column-email" data-colname="Email">
                    <a href="mailto:<?php $user->user_email?>"><?php echo  $user->user_email ?>
                </a></td><td class="role column-role" data-colname="Role"><?php echo $user_roles ?>
            </td><td class="posts column-posts num" data-colname="Posts"><div id="button-<?php echo $user->ID ?>" onclick="sendMail(<?php echo $user->ID ?>,'<?php echo $user->user_nicename ?>','<?php echo $user->user_email?>',<?php echo $stRefcode ?>)" class="edit">
                <span aria-hidden="true" style="cursor: pointer;color:blue" id="invite-<?php echo $user->ID ?>"><?php echo $invitedtext ?></span><span class="screen-reader-text"></span>
    </div></td></tr>
         <?php
    }
        }
    }
     
        
        else {
            echo 'No users found.';
        } 
    
 ?>           
</tbody>
</table>
<style>
    .page-numbers
    {
        display:flex;
        gap:5px;
    }
    </style>
<?php
            $total_user = $user_query->total_users;  
            $total_pages=ceil($total_user/$no);?><div class="tablenav top" style="display:flex">
                <?php

              echo paginate_links(array(  
                  'base' => get_pagenum_link(1) . '%_%',  
                  'format' => '?paged=%#%',  
                  'current' => $paged,  
                  'total' => $total_pages,  
                  'prev_text' => 'Previous',  
                  'next_text' => 'Next',
                  'type'     => 'list',
                )); ?></div>

<?php
}
function addCoseller()
{

}
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>

function sendMail(id,name,mail,refcode) {
    var link="<?php echo plugin_dir_path( __FILE__ ).'/shortcodes/coselluserlist.php' ?>"
    jQuery('#invite-'+id).html('wait...');
  jQuery.ajax({
    url:link,
   type: 'post',
   data: {id:id,name:name,mail:mail,refcode:refcode},
   success: function(){
    jQuery('#invite-'+id).html('success');
   },
   error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert("Status: " + textStatus); alert("Error: " + errorThrown); 
    }  
  });
}

function sendInviteAll()
{
    
    var inputs = document.querySelectorAll('.inviteallcheck');   
        for (var i = 0; i < inputs.length; i++) {   
            if(inputs[i].checked == true) 
            {
                let text = inputs[i].getAttribute("data");
                document.getElementById("button-"+i).click()
                console.log(text);

            }  
        }   
}
</script>
