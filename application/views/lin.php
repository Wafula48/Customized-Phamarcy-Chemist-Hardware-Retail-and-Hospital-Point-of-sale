<?php
// Place this block at the top of your view/controller file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = uniqid(); // Or fetch your unique product code here
    $product_id = 1;  // Replace with actual product ID logic

    if (!empty($_FILES['uploadFile']['name'])) {
        $file_name = $_FILES['uploadFile']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = $code . '.' . $file_ext;

        // Upload configuration
        $config['upload_path'] = './assets/upload/products/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = $new_file_name;

        $CI = &get_instance();
        $CI->load->library('upload', $config);

        if ($CI->upload->do_upload('uploadFile')) {
            echo "File uploaded successfully.<br>";

            $width_array = array(100, 200);
            $height_array = array(100, 200);
            $dir_array = array('xsmall', 'small');

            $CI->load->library('image_lib');

            for ($i = 0; $i < count($width_array); $i++) {
                $thumbnail_dir = './assets/upload/products/' . $dir_array[$i] . '/' . $code;
                if (!file_exists($thumbnail_dir)) {
                    mkdir($thumbnail_dir, 0777, true);
                }

                $resize_config['image_library'] = 'gd2';
                $resize_config['source_image'] = './assets/upload/products/' . $new_file_name;
                $resize_config['maintain_ratio'] = true;
                $resize_config['width'] = $width_array[$i];
                $resize_config['height'] = $height_array[$i];
                $resize_config['quality'] = '100%';
                $resize_config['new_image'] = $thumbnail_dir . '/' . $new_file_name;

                $CI->image_lib->clear();
                $CI->image_lib->initialize($resize_config);

                if ($CI->image_lib->resize()) {
                    echo "Thumbnail created: " . $resize_config['new_image'] . "<br>";
                } else {
                    echo "Resize Error: " . $CI->image_lib->display_errors();
                }
            }

            $original_file_path = './assets/upload/products/' . $new_file_name;
            if (file_exists($original_file_path)) {
                unlink($original_file_path);
                echo "Original file deleted: $original_file_path<br>";
            }

            // Save thumbnail filename to database
            $upd_file_data = array('thumbnail' => $new_file_name);
            if ($CI->Constant_model->updateData('products', $upd_file_data, $product_id)) {
                echo "Database updated with thumbnail: $new_file_name<br>";
            } else {
                echo "Failed to update database.<br>";
            }
        } else {
            echo "Upload Error: " . $CI->upload->display_errors();
        }
    } else {
        echo "No file uploaded.<br>";
    }
}
?>

<!-- HTML form -->
<form method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="uploadFile" class="form-control" accept="image/jpeg, image/png" required />
            </div>
        </div>
        <div class="col-md-4 mt-3">
            <button type="submit" class="btn btn-primary">Upload</button>
        </div>
    </div>
</form>