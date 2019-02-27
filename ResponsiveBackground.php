<?php


class BackgroundHelper {

	public function __construct($imageID) {

        $sizes = $GLOBALS['ce_config']['sizes'];
        $breakpoints = $GLOBALS['ce_config']['breakpoints'];

    	$this->sizes = $sizes;
    	$this->breakpoints = $breakpoints;
    	$this->srcs = $this->get_all_images($imageID);
    }

    public function get_all_images($imageID) {
    	$srcs = array();
    	foreach ($this->sizes as $size) {
    		$srcs[$size] = wp_get_attachment_image_src($imageID, $size)[0];
    	}
        return $srcs;
    }

    public function get_styles() {
        $srcs = $this->srcs;
        if (!is_array($srcs)) return;

        foreach ($this->breakpoints as $breakpoint => $size) {
            $styles[$breakpoint] = $srcs[$size];
        }
        return $styles;
    }

}

function ce_bg_styles($imageID) {
    $selector = uniqid('bg-');
    $bg = new BackgroundHelper($imageID);
    return ['selector' => $selector, 'styles' => $bg->get_styles()];
}


function ce_bg($imageID, $max = 9999, $min = 0) {
    global $ce_bgs;
    $bgs = ce_bg_styles($imageID);
    $styles = "";
    $full_selector = "data-cebg='" . $bgs['selector'] ."'";
    foreach ($bgs['styles'] as $size => $bg) {
        if ($size <= $max AND $size >= $min) {
            if($size == 0) {
            $styles .= "[". $full_selector . "] { background-image:url(".$bg.");} \n";
            }else {
                $styles .= "@media screen and (min-width: ". $size ."px) { [". $full_selector . "] { background-image:url(".$bg.");} } \n";
            }
        }
    }
    $ce_bgs[$bgs['selector']] = $styles;
    echo $full_selector;
}


add_action('wp_footer', 'ce_bg_print');
function ce_bg_print() {
    global $ce_bgs;
    if(!$ce_bgs) return;
    //$styles = implode("\n", $ce_bgs? : []);
    //$styles = sprintf("<style type=\"text/css\">\n%s\n</style>", $styles);
    echo "<style type='text/css'> \n";
    foreach ($ce_bgs as $ce_bg) {
        echo $ce_bg;
    }
    echo "</style>";
}







