<?php /* Form fields. */?>
<table class="form-table">
  <tr>
    <th>
      <label  for="marcador_mail_name" 
              class="marcador_mail_name_label">
                <?php echo __( 'Name', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="text"  readonly 
              id="marcador_mail_name" 
              name="marcador_mail_name" 
              class="marcador_mail_name_field" 
              placeholder="<?php echo esc_attr__( '', 'text_domain' ); ?>" 
              value="<?php echo esc_attr__( $marcador_mail_name ); ?>" />
    </td>
  </tr>
  <tr>
    <th>
      <label  for="marcador_mail_email" 
              class="marcador_mail_email_label">
                <?php echo __( 'Email', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="email" readonly 
              id="marcador_mail_email" 
              name="marcador_mail_email" 
              class="marcador_mail_email_field" 
              placeholder="<?php echo esc_attr__( '', 'text_domain' ); ?>" 
              value="<?php echo esc_attr__( $marcador_mail_email ); ?>" />
    </td>
  </tr>
  <tr>
    <th>
      <label  for="marcador_mail_phone" 
              class="marcador_mail_phone_label">
                <?php echo __( 'Phone', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="phone" readonly 
              id="marcador_mail_phone" 
              name="marcador_mail_phone" 
              class="marcador_mail_phone_field" 
              placeholder="<?php echo esc_attr__( '', 'text_domain' ); ?>" 
              value="<?php echo esc_attr__( $marcador_mail_phone ); ?>" />
    </td>
  </tr>
  <tr>
    <th>
      <label  for="marcador_mail_asunto" 
              class="marcador_mail_asunto_label">
                <?php echo __( 'Asunto', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="number" readonly 
              id="marcador_mail_asunto" 
              name="marcador_mail_asunto" 
              class="marcador_mail_asunto_field" 
              placeholder="<?php echo esc_attr__( '', 'text_domain' ); ?>" 
              value="<?php echo esc_attr__( $marcador_mail_asunto ); ?>" />
    </td>
  </tr>
<?php /*  <tr>
    <th>
      <label  for="car_cruise_control" 
              class="car_cruise_control_label">
                <?php echo __( 'Cruise Control', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="checkbox" 
              id="car_cruise_control" 
              name="car_cruise_control" 
              class="car_cruise_control_field" 
              value="<?php echo $car_cruise_control; ?>" 
              <?php echo checked( $car_cruise_control, 'checked', false ); ?>/>
                <?php echo __( '', 'text_domain' ); ?>
      <span class="description">
        <?php echo __( 'Car has cruise control.', 'text_domain' ); ?>
      </span>
    </td>
  </tr>
  <tr>
    <th>
      <label  for="car_power_windows" 
              class="car_power_windows_label">
              <?php echo __( 'Power Windows', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="checkbox" 
              id="car_power_windows" 
              name="car_power_windows" 
              class="car_power_windows_field" 
              value="<?php echo $car_power_windows; ?>"
              <?php echo checked( $car_power_windows, 'checked', false ); ?> />
                <?php echo __( '', 'text_domain' ); ?>
      <span class="description">
        <?php echo __( 'Car has power windows.', 'text_domain' ); ?>
      </span>
    </td>
  </tr>
  <tr>
    <th>
      <label  for="car_sunroof" 
              class="car_sunroof_label">
              <?php echo __( 'Sunroof', 'text_domain' ); ?>
      </label>
    </th>
    <td>
      <input  type="checkbox" 
              id="car_sunroof" 
              name="car_sunroof" 
              class="car_sunroof_field" 
              value="' . $car_sunroof . '" 
              <?php echo checked( $car_sunroof, 'checked', false ); ?> /> 
                <?php echo __( '', 'text_domain' ); ?>
      <span class="description">
        <?php echo __( 'Car has sunroof.', 'text_domain' ); ?>
      </span>
    </td>
  </tr> */ ?>
</table>