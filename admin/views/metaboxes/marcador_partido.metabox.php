<?php /* Form fields. */?>
<table class="form-table">
  <tr>
    <th>
      <label  for="marcador_sp_game_id" 
              class="marcador_sp_game_id_label">
                <?php echo __( 'Id', 'marcadordo' ); ?>
      </label>
    </th>
    <td>
      <code><?php echo esc_attr__( $game_id ); ?></code>
      <?php /*<input  type="text"  readonly 
              id="marcador_sp_game_id" 
              name="marcador_sp_game_id" 
              class="marcador_sp_game_id_field regular-text" 
              placeholder="<?php echo esc_attr__( '', 'marcadordo' ); ?>" 
              value="<?php echo esc_attr__( $game_id ); ?>" /> */ ?>
    </td>
  </tr>
  <tr>
    <th>
      <label  for="marcador_sp_game_data" 
              class="marcador_sp_game_data_label">
                <?php echo __( 'Data', 'marcadordo' ); ?>
      </label>
    </th>
    <td>
      <?php $data = json_decode( $game_data ); ?>
      <label><?php echo esc_attr__( $data->home->name ); ?> (<?php echo esc_attr__( $data->home->abbr ); ?>): </label><code><?php echo esc_attr__( $data->home->runs ); ?></code><br>
      <label><?php echo esc_attr__( $data->away->name ); ?> (<?php echo esc_attr__( $data->away->abbr ); ?>): </label><code><?php echo esc_attr__( $data->away->runs ); ?></code><br>
      <?php /*<textarea type="email" readonly rows="10"
                id="marcador_sp_game_data"
                name="marcador_sp_game_data"
                class="marcador_sp_game_data_field large-text code" 
                placeholder="<?php echo esc_attr__( '', 'marcadordo' ); ?>"><?php echo esc_attr__( $game_data ); ?></textarea> */?>
    </td>
  </tr>
  <tr>
    <th>
      <label  for="marcador_sp_game_status" 
              class="marcador_sp_game_status_label">
                <?php echo __( 'Status', 'marcadordo' ); ?>
      </label>
    </th>
    <td>
      <code><?php echo esc_attr__( $game_status ); ?></code>
      <?php /*<input  type="phone" readonly 
              id="marcador_sp_game_status" 
              name="marcador_sp_game_status" 
              class="marcador_sp_game_status_field regular-text" 
              placeholder="<?php echo esc_attr__( '', 'marcadordo' ); ?>" 
              value="<?php echo esc_attr__( $game_status ); ?>" /> */ ?>
    </td>
  </tr>
</table>