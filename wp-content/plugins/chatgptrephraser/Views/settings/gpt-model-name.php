<select  class="form-select ps-4 text-dark" name="gpt_model_name" id="gpt_model_name">
    <option value="">Select a Model</option>
    <?php foreach ($all_models as $model) { ?>
        <option value="<?php echo $model['id']; ?>" <?php echo selected($model['model_name'], $selected_model); ?>>
            <?php echo esc_html($model['model_name']); ?>
        </option>
    <?php } ?>
</select>
