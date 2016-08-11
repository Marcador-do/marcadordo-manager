<div class="wrap">
  <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  <p>General settings.</p>
  <form method="post" action="options.php" novalidate="novalidate">
    <?php settings_fields( 'marcadordo-general-settings' ); ?>
    <?php do_settings_sections( 'marcadordo-general-settings' ); ?>
    <?php submit_button(); ?>
  </form>
</div>