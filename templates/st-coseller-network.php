<?php
/*
 * Template name: Shoptype Referral Tree
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 *@package shoptype
 */
global $stBackendUrl;

$st_token = $_COOKIE["stToken"];
$args = array( 
			'headers' => array( 
				'Authorization' => $st_token,
			) 
		);
try {
	$response = wp_remote_get("{$stBackendUrl}/referrals/", $args);
	$referralTree = wp_remote_retrieve_body( $response );
	
	if (!empty($referralTree)) {
		$st_referralTree = json_decode($referralTree);
		if(!isset($st_referralTree->data)){
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			get_template_part( 404 );
			exit();
		}
		$platformInvitees = array();

		foreach($st_referralTree->data->inviter as $inviter){
			if($inviter->platformId){
				$platformInviter = $inviter;
			}
		}
		foreach($st_referralTree->data->invitees as $invitee){
			if($invitee->platformId){
				array_push($platformInvitees,$invitee);
			}
		}
	}
} catch (Exception $e) {
}

get_header();
?>

<div class="st-coseller-network">
	<h1>
		My Network
	</h1>
	<div class="st-coseller-inviter">
		<div class="st-coseller-details">
			<h3>Inviter</h3>
			<div class="st-coseller-name"><?php echo "<span>Name:</span> {$platformInviter->name}"; ?></div>
			<div class="st-coseller-email"><?php echo "<span>Email:</span> {$platformInviter->email}"; ?></div>
			<div class="st-coseller-ref"><?php echo "<span>Invitees Count:</span> {$platformInviter->inviteesCount}"; ?></div>
		</div>
	</div>

	<div class="st-coseller-me">
		<div class="st-coseller-details">
			<h3>You</h3>
			<div class="st-coseller-name"><?php echo "<span>Name:</span> {$st_referralTree->data->me->name}"; ?></div>
			<div class="st-coseller-email"><?php echo "<span>Email:</span> {$st_referralTree->data->me->email}"; ?></div>
			<div class="st-coseller-ref"><?php echo "<span>Invitees Count:</span> {$st_referralTree->data->me->inviteesCount}"; ?></div>
		</div>
	</div>
	<div class="st-coseller-invitees">
		<?php 
		if(isset($st_referralTree->data->inviter)){
		foreach($platformInvitees as $invitee){ ?>
		<div class="st-coseller-details">
			<h3>Invitees</h3>
			<div class="st-coseller-name"><?php echo "<span>Name:</span> {$invitee->name}"; ?></div>
			<div class="st-coseller-email"><?php echo "<span>Email:</span> {$invitee->email}"; ?></div>
			<div class="st-coseller-ref"><?php echo "<span>Invitees Count:</span> {$invitee->inviteesCount}"; ?></div>
		</div>
		<?php }
		}
		?>
	</div>
</div>
<style>
	.st-coseller-network h1{
		text-align: center;
	}
	.st-coseller-network{
		margin-top:20px;
	}
	.st-coseller-details h3 {
		font: 400 19px / 20px sans-serif;
		text-align: center;
		margin:-10px -10px 0px -10px;
		padding: 5px;
		background: #333333;
		color:#ffffff;
	}
	.st-coseller-inviter, .st-coseller-me, .st-coseller-invitees {
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
	}
	.st-coseller-me .st-coseller-details {
		background: #e9ffff;
	}
	.st-coseller-invitees .st-coseller-details {
		background: #ffc9ff;
	}
	.st-coseller-details span {
		font-weight:600;
	}
	.st-coseller-details {
		display: flex;
		flex-direction: column;
		margin: 10px;
		padding: 10px;
		background: #e9e9ff;
		border: solid 1px #AAAAAA;
		border-radius: 10px;
		width: 340px;
		overflow:hidden;
		max-width:calc(100% - 20px);
	}
</style>

<?php
get_footer();