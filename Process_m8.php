<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/astro-modern.css">

<main class="astro-card" id="main-content">
    <section class="astro-section">
        <h2 style="margin-bottom:0.5em;">Image Processing Example: m8 (Lagoon Nebula)</h2>
        <p class="astro-subtitle"><i>Step-by-step processing of m8, from raw frames to finished image. Click images to enlarge.</i></p>
        <div style="margin-bottom:2em;">
            <a href="Nebulae.php" class="astro-btn">&larr; Back to Nebulae</a>
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
            <h3>Step 1: Luminance File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Luminance</b> file: Stack of 26 sub-frames, 1 min each, unbinned, SBIG Clear filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_L_1280.jpg" target="_blank"><img src="process/m8_L_200.jpg" alt="Luminance"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 2: Hydrogen-Alpha File</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Hydrogen-Alpha</b> file: Stack of 5 sub-frames, 15 min each, unbinned, Astrodon H-alpha filter.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_H_1280.jpg" target="_blank"><img src="process/m8_H_200.jpg" alt="Hydrogen-Alpha"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 3: Red, Green, Blue Files</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    <b>Red</b>, <b>Green</b>, <b>Blue</b> files: Each is a stack of 9 sub-frames, 1 min each, unbinned, through respective filters.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_R_1280.jpg" target="_blank"><img src="process/m8_R_200.jpg" alt="Red"></a>
                    <a href="process/m8_G_1280.jpg" target="_blank"><img src="process/m8_G_200.jpg" alt="Green"></a>
                    <a href="process/m8_B_1280.jpg" target="_blank"><img src="process/m8_B_200.jpg" alt="Blue"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 4: Deconvolution (CCDSharp)</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Use CCDSharp to perform 3-iteration Richardson-Lucy Deconvolution on Luminance and H-alpha frames.<br>
                    <a href="Process_CCDSharp_Deconvolution.php">Learn more about this step</a>.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_L_LR3_1280.jpg" target="_blank"><img src="process/m8_L_LR3_200.jpg" alt="Luminance Deconvolved"></a>
                    <a href="process/m8_H_LR3_1280.jpg" target="_blank"><img src="process/m8_H_LR3_200.jpg" alt="H-alpha Deconvolved"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 5: Non-Linear Stretch (MaxIm DL)</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Use MaxIm DL to apply Non-Linear Stretch (DDP) to Luminance, H-alpha, and RGB frames.<br>
                    <a href="Process_MaxImDL_DDP.php">Learn more about DDP</a>.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_L_LR3_DDP_1280.jpg" target="_blank"><img src="process/m8_L_LR3_DDP_200.jpg" alt="Luminance DDP"></a>
                    <a href="process/m8_H_LR3_DDP_1280.jpg" target="_blank"><img src="process/m8_H_LR3_DDP_200.jpg" alt="H-alpha DDP"></a>
                    <a href="process/m8_RGB_Setup3_1280.jpg" target="_blank"><img src="process/m8_RGB_Setup3_200.jpg" alt="RGB DDP"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 6: Color Combine (MaxIm DL)</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Combine R, G, B frames in MaxIm DL. Adjust color mixing ratios and background equalization as needed.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_RGB_Setup1_1280.jpg" target="_blank"><img src="process/m8_RGB_Setup1_200.jpg" alt="RGB Combine"></a>
                    <a href="process/m8_RGB_Setup2_1280.jpg" target="_blank"><img src="process/m8_RGB_Setup2_200.jpg" alt="RGB Pre-Stretch"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 7: Photoshop Merge &amp; Finishing</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Merge Luminance and H-alpha in Photoshop using Lighten blend mode. Flatten layers, then use Inverted Layer Mask and Gaussian Blur to reduce background noise. Repeat for RGB as needed.<br>
                    <a href="Process_PS_ILM.php">Learn more about Inverted Layer Mask</a>.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_HaL_Merge1_1280.jpg" target="_blank"><img src="process/m8_HaL_Merge1_200.jpg" alt="HaL Merge 1"></a>
                    <a href="process/m8_HaL_Merge2_1280.jpg" target="_blank"><img src="process/m8_HaL_Merge2_200.jpg" alt="HaL Merge 2"></a>
                    <a href="process/m8_HaL_Merge3_1280.jpg" target="_blank"><img src="process/m8_HaL_Merge3_200.jpg" alt="HaL Merge 3"></a>
                    <a href="process/m8_HaL_Merge4_1280.jpg" target="_blank"><img src="process/m8_HaL_Merge4_200.jpg" alt="HaL Merge 4"></a>
                </div>
            </div>
        </div>
        <div class="astro-step">
            <h3>Step 8: Final Noise Reduction</h3>
            <div class="astro-step-content">
                <div class="astro-step-text">
                    Use Inverted Layer Mask and Gaussian Blur to reduce noise in both HaL and RGB images. Adjust Levels as needed for best results.
                </div>
                <div class="astro-step-image">
                    <a href="process/m8_HaL_Noise1_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise1_200.jpg" alt="HaL Noise 1"></a>
                    <a href="process/m8_HaL_Noise2_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise2_200.jpg" alt="HaL Noise 2"></a>
                    <a href="process/m8_HaL_Noise3_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise3_200.jpg" alt="HaL Noise 3"></a>
                    <a href="process/m8_HaL_Noise4_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise4_200.jpg" alt="HaL Noise 4"></a>
                    <a href="process/m8_HaL_Noise5_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise5_200.jpg" alt="HaL Noise 5"></a>
                    <a href="process/m8_HaL_Noise6_1280.jpg" target="_blank"><img src="process/m8_HaL_Noise6_200.jpg" alt="HaL Noise 6"></a>
                    <a href="process/m8_RGB_Noise0_1280.jpg" target="_blank"><img src="process/m8_RGB_Noise0_200.jpg" alt="RGB Noise 0"></a>
                    <a href="process/m8_RGB_Noise1_1280.jpg" target="_blank"><img src="process/m8_RGB_Noise1_200.jpg" alt="RGB Noise 1"></a>
                    <a href="process/m8_RGB_Noise2_1280.jpg" target="_blank"><img src="process/m8_RGB_Noise2_200.jpg" alt="RGB Noise 2"></a>
                    <a href="process/m8_RGB_Noise3_1280.jpg" target="_blank"><img src="process/m8_RGB_Noise3_200.jpg" alt="RGB Noise 3"></a>
                    <a href="process/m8_RGB_Noise4_1280.jpg" target="_blank"><img src="process/m8_RGB_Noise4_200.jpg" alt="RGB Noise 4"></a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
