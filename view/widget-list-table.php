<?php 
/**
 * Widget lists table
 * 
 * @author ronym <ronymaha@gmail.com>
 */
?>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th><?php _e('Name', 'wedevs'); ?></th>
                <th><?php _e('Email', 'wedevs'); ?></th>
                <th><?php _e('CV', 'wedevs'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($db_applications as $single): ?>
                <tr>
                    <td><?php echo sprintf('%s %s', esc_attr($single->first_name), esc_attr($single->last_name)) ?></td>
                    <td><?php echo esc_attr($single->email); ?></td>
                    <td><a download href="<?php echo esc_url($single->cv); ?>"><span class="dashicons dashicons-pdf"></span></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>