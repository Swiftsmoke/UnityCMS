<form method="post" action="https://www.paypal.com/cgi-bin/webscr/?cmd=_xclick">
    <input type="hidden" name="custom" value="<?php echo Page::$custom; ?>" readonly />
    <input type="hidden" name="business" value="<?php echo Page::$business; ?>" readonly />
    <input type="hidden" name="item_number" value="1" readonly />
    <input type="hidden" name="item_name" value="Donate to <?php echo Page::$sitename; ?>" readonly />
    <input type="hidden" name="on0" value="0" readonly />
    <input type="hidden" name="currency_code" value="USD" readonly />
    <input type="hidden" name="return" value="<?php echo ROOT_URL; ?>?donate&success=1" readonly />
    <input type="hidden" name="cancel_return" value="<?php echo ROOT_URL; ?>" readonly />    
    <?php echo Page::$notify; ?>
    <label for="amount">Donation Amount <small>(1 = $1)</small></label><br>
    <input type="text" name="amount" value="1" size="5" style="text-align: center" /><br><br>
    <input type="submit" name="submit" value="Donate" /><br><br>
</form>
