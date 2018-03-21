<?php
if(isset($_POST['exHideMsg'])){
    update_option('exammatrix_donation_camp','Y');
}
$status = get_option('exammatrix_donation_camp');
if($status == 'N'){ ?>
<div class="donation-wrap postbox">
    <h3 class="hndle"><span>Upcoming</span></h3>
    <div class="inside">
        <div class="main">
            <p>We are going to add Android support to this plugin and you will also have a
                android app taking exams and publishing with your blog name and logo .</p><p>
                other updates are result printing, graph generation, simple survey etc, 
                You can also send what you have in your mind to make it better</p>
            <p> I have to spend more time on it and i am sure that you can buy a coffee for me, just donate to "dhupadevirawat@gmail.com" Thank you !!<br/>
            </p>
            <form method="post">
                <input type="submit" class="button" value="Hide This Message Forever !!" name="exHideMsg" />
            </form>
        </div>
    </div>
</div>
<?php } ?>