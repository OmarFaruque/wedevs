<?php
/**
 * Application form 
 * 
 * @author Omar <ronymaha@gmail.com>
 */
?>

<?php
global $wderror;


//Success message
if ($formProcess) {
?>
<div class="success">
    <span>
        <?php _e('Your application submitted successfully.', 'wedevs'); ?>
    </span>
</div>
<?php
}



//Error lists
if (isset($wderror) && count($wderror) > 0) {
    ?>
<div class="error">
    <ul>
        <?php foreach ($wderror as $single_error): ?>
        <li>
            <?php echo esc_attr($single_error); ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php
}
?>
<form action="" method="post" enctype="multipart/form-data">
    <div class="applicationWrap">
        <div class="row">

            <div class="form-group">
                <label for="first_name">
                    <?php esc_attr_e('First Name', 'wedevs'); ?>
                </label>
                <input title="<?php esc_attr_e('Allow letter only', 'wedevs'); ?>" pattern="^[A-Za-z \s*]+$" type="text"
                    name="first_name" id="first_name"
                    value="<?php isset($_POST['first_name']) ? _e($_POST['first_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="last_name">
                    <?php esc_attr_e('Last Name', 'wedevs'); ?>
                </label>
                <input pattern="^[A-Za-z \s*]+$" type="text" name="last_name" id="last_name"
                    value="<?php isset($_POST['last_name']) ? _e($_POST['last_name']) : ''; ?>">
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="paresent_address">
                    <?php esc_attr_e('Present Address', 'wedevs'); ?>
                </label>
                <input type="text" name="present_address" id="present_address"
                    value="<?php isset($_POST['present_address']) ? _e($_POST['present_address']) : ''; ?>">
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="email">
                    <?php esc_attr_e('Email Address', 'wedevs'); ?>
                </label>
                <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" name="email" id="email"
                    value="<?php isset($_POST['email']) ? _e($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="mobile">
                    <?php esc_attr_e('Mobile Number', 'wedevs'); ?>
                </label>
                <input type="tel" name="mobile" id="mobile"
                    value="<?php isset($_POST['mobile']) ? _e($_POST['mobile']) : ''; ?>">
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="post_name">
                    <?php esc_attr_e('Post Name', 'wedevs'); ?>
                </label>
                <input type="text" name="post_name" id="post_name"
                    value="<?php isset($_POST['post_name']) ? $_POST['post_name'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="cv">
                    <?php esc_attr_e('CV', 'wedevs'); ?>
                </label>
                <input type="file" accept="application/pdf"
                    placeholder="<?php esc_attr_e('Upload your CV', 'wedevs'); ?>" name="cv" id="cv">
            </div>
        </div>
        <?php wp_nonce_field('wdapplication', 'wd_nonce'); ?>

        <div class="submit mt-20">
            <input type="submit" name="application_submit" class="btn btn-primary"
                value="<?php esc_attr_e('Submit', 'wedevs'); ?>">
        </div>
    </div>
</form>