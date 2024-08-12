  <div class="wrap">
      <h1>Stripe Subscription Manager</h1>

      <?php if ($message != '') : ?>
          <div class="notice notice-<?= $status ?>">
              <p><?= $message ?></p>
          </div>
      <?php endif; ?>

      <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
          <input type="hidden" name="action" value="save_stripe_settings">
          <?php wp_nonce_field('stripe_settings_save', 'stripe_settings_nonce'); ?>

          <table class="form-table">
              <tr valign="top">
                  <th scope="row">Stripe Publishable Key</th>
                  <td><input required style="width:100%" type="text" name="stripe_publishable_key" value="<?php echo esc_attr($publishable_key); ?>" /></td>
              </tr>
              <tr valign="top">
                  <th scope="row">Stripe Secret Key</th>
                  <td><input required style="width:100%" type="text" name="stripe_secret_key" value="<?php echo esc_attr($secret_key); ?>" /></td>
              </tr>

              <tr valign="top">
                  <th scope="row">Stripe Webhook Secret Key</th>
                  <td><input required style="width:100%" type="text" name="stripe_webhook_secret" value="<?php echo esc_attr($webhook_secret); ?>" /></td>
              </tr>

              <tr valign="top">
                  <th scope="row">On Trail Max Post Limit</th>
                  <td><input required style="width:100%" type="text" name="generic_trail_limit" value="<?php echo esc_attr($trail); ?>" /></td>
              </tr>
              <tr valign="top">
                  <th scope="row">Trial(No Of Days)</th>
                  <td><input required style="width:100%" type="text" name="no_of_days" value="<?php echo esc_attr($no_of_days); ?>" /></td>
              </tr>
          </table>

          <?php submit_button('Save Settings'); ?>
      </form>
  </div>