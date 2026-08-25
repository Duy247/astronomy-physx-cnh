<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/astro-modern.css">

<main class="astro-card" id="main-content">
    <section class="astro-section">
        <h2 style="margin-bottom:0.5em;">Image Processing Example: m63 (Sunflower Galaxy)</h2>
        <p class="astro-subtitle"><i>Step-by-step processing of m63, from raw frames to finished image. Click images to enlarge.</i></p>
        <div style="margin-bottom:2em;">
            <a href="/Galaxies.php" class="astro-btn">&larr; Back to Galaxies</a>
        </div>
        <div class="astro-step">
            <h3>Step 0: Images ready to process</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    These image files were taken with an Optical Guidance Systems 32" Ritchey-Chretien telescope and SBIG STL-11000m camera, from very dark and transparent skies in northwestern Arizona.<br><br>
                    The raw files were calibrated (Dark Subtracted, Flat Fielded, Hot/Cold Pixels removed), and aligned with each other.
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Luminance File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Luminance</b> file: Stack of 12 sub-frames, 5 min each, unbinned, Astrodon Clear filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_L_1280.jpg" target="_blank"><img src="process/m63_L_200.jpg" alt="Luminance"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Red File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Red</b> file: Stack of 4 sub-frames, 5 min each, unbinned, Astrodon Red filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_R_1280.jpg" target="_blank"><img src="process/m63_R_200.jpg" alt="Red"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Green File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Green</b> file: Stack of 4 sub-frames, 5 min each, unbinned, Astrodon Green filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_G_1280.jpg" target="_blank"><img src="process/m63_G_200.jpg" alt="Green"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Blue File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Blue</b> file: Stack of 4 sub-frames, 5 min each, unbinned, Astrodon Blue filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_B_1280.jpg" target="_blank"><img src="process/m63_B_200.jpg" alt="Blue"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 1: Deconvolve Luminance File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Use CCDSharp to do a 3-iteration <a href="Process_CCDSharp_Deconvolution.php">Richardson-Lucy Deconvolution</a> on the Luminance frame. Luminance file shown after deconvolution at right.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_L_LR3_1280.jpg" target="_blank"><img src="process/m63_L_LR3_200.jpg" alt="Luminance Deconvolved"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 2: Non-Linear Stretch of Luminance File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Use MaxIm DL to do a <a href="Process_MaxImDL_DDP.php">Non-Linear Stretch (DDP)</a> on the Luminance frame. DDP parameters: Background = 4200, Mid-Level = 5400. Luminance file shown after non-linear stretch at right.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_L_LR3_DDP_1280.jpg" target="_blank"><img src="process/m63_L_LR3_DDP_200.jpg" alt="Luminance DDP"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 3: Color Combine Red/Green/Blue frames and do Non-Linear Stretch on result</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    In MaxIm DL, load all three color files and select <b>Color | Combine Color</b>.<br>
                    Set the color mixing ratio for each filter. Example: 1.00 - Red, 1.20 - Green, 1.20 - Blue. Check the <b>Bgd Auto Equalize</b> button.<br>
                    After color combine, do a non-linear stretch (DDP) on the RGB frame. DDP parameters: Background = 1000, Mid-Level = 1300. Save as 16-bit TIFF.<br>
                    The color here may seem washed out, but will be improved in Photoshop.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_RGB_Setup1_1280.jpg" target="_blank"><img src="process/m63_RGB_Setup1_200.jpg" alt="RGB Combine"></a>
                    <a href="process/m63_RGB_Setup2_1280.jpg" target="_blank"><img src="process/m63_RGB_Setup2_200.jpg" alt="RGB Pre-Stretch"></a>
                    <a href="process/m63_RGB_Setup3_1280.jpg" target="_blank"><img src="process/m63_RGB_Setup3_200.jpg" alt="RGB DDP"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 4: Load Luminance into Photoshop, Reduce Background Noise using Inverted Layer Mask</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Load the non-linear stretched Luminance frame into Photoshop. Use <a href="Process_PS_ILM.php">Inverted Layer Mask</a> and Gaussian Blur to reduce noise in the low-signal areas, protecting stars and details. Adjust Levels as needed.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_L_Noise0_1280.jpg" target="_blank"><img src="process/m63_L_Noise0_200.jpg" alt="Luminance Noise 0"></a>
                    <a href="process/m63_L_Noise1_1280.jpg" target="_blank"><img src="process/m63_L_Noise1_200.jpg" alt="Luminance Noise 1"></a>
                    <a href="process/m63_L_Noise2_1280.jpg" target="_blank"><img src="process/m63_L_Noise2_200.jpg" alt="Luminance Noise 2"></a>
                    <a href="process/m63_L_Noise3_1280.jpg" target="_blank"><img src="process/m63_L_Noise3_200.jpg" alt="Luminance Noise 3"></a>
                    <a href="process/m63_L_Noise4_1280.jpg" target="_blank"><img src="process/m63_L_Noise4_200.jpg" alt="Luminance Noise 4"></a>
                    <a href="process/m63_L_Noise5_1280.jpg" target="_blank"><img src="process/m63_L_Noise5_200.jpg" alt="Luminance Noise 5"></a>
                    <a href="process/m63_L_Noise6_1280.jpg" target="_blank"><img src="process/m63_L_Noise6_200.jpg" alt="Luminance Noise 6"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 5: Load RGB Image, Reduce Color Noise using Inverted Layer Mask</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Load the non-linear stretched RGB image. Use <a href="Process_PS_ILM.php">Inverted Layer Mask</a> (using the Luminance image as the mask) and Gaussian Blur to reduce color noise. Adjust Levels as needed.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_RGB_Noise0_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise0_200.jpg" alt="RGB Noise 0"></a>
                    <a href="process/m63_RGB_Noise1_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise1_200.jpg" alt="RGB Noise 1"></a>
                    <a href="process/m63_RGB_Noise2_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise2_200.jpg" alt="RGB Noise 2"></a>
                    <a href="process/m63_RGB_Noise3_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise3_200.jpg" alt="RGB Noise 3"></a>
                    <a href="process/m63_RGB_Noise4_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise4_200.jpg" alt="RGB Noise 4"></a>
                    <a href="process/m63_RGB_Noise5_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise5_200.jpg" alt="RGB Noise 5"></a>
                    <a href="process/m63_RGB_Noise6_1280.jpg" target="_blank"><img src="process/m63_RGB_Noise6_200.jpg" alt="RGB Noise 6"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 6: Merge Luminance image into RGB image</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    In Photoshop, copy the Luminance image and paste it onto the RGB image. Set the blend mode to <b>Luminosity</b>. Adjust color saturation as needed (<b>Image | Adjustments | Hue/Saturation</b>). Flatten layers to make the merger permanent.
                </div>
                <div class="astro-step-image">
                    <a href="process/m63_LRGB_Merge1_1280.jpg" target="_blank"><img src="process/m63_LRGB_Merge1_200.jpg" alt="LRGB Merge 1"></a>
                    <a href="process/m63_LRGB_Merge2_1280.jpg" target="_blank"><img src="process/m63_LRGB_Merge2_200.jpg" alt="LRGB Merge 2"></a>
                    <a href="process/m63_LRGB_Merge3_1280.jpg" target="_blank"><img src="process/m63_LRGB_Merge3_200.jpg" alt="LRGB Merge 3"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 7: Final Adjustments/Tweaking Image</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Final adjustments are subjective and depend on the imager's preferences. Adjust brightness, contrast, and color to achieve the desired final result.
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
