<?php

// ==================================================================
// | UrPCMS : Your Powerful Content Management System
// ==================================================================
// |
// | Copyright (c) 2003 UrStudios.Net by Bruno Cluizel
// | http://www.urpcms.com
// |
// | This program is free software; you can redistribute it and/or
// | modify it under the terms of the GNU General Public License as
// | as published by the Free Software Foundation; either version 2
// | of the License.
// | 
// | All Border Themes must contain a file named [cfg.php]. This
// | file contains the different parameters to display a panel.
// |
// | THEME SYSTEM VERSION : 1.1
// ==================================================================

class ur_theme {
	var $title_display;				// True if the title must be displayed
	var $title_color;				// Title color
	var $title_bold;				// True if the title must be bold
	
	var $tbar_size;					// Height of the upper titlebar
									// - If this value is equal to 0, the title text is displayed in the edge.
	var $tbar_offset;				// Space between titlebar and panel (Only used if [tbar_size_cnt] > 0)
	var $tbar_pic;					// Upper titlebar background picture

	var $edge_pic_cnt;				// Picture names for the center panels
	var $edge_pic_side;				// Picture names for the side panels
	var $edge_size_cnt;				// Border sizes for the center panels
	var $edge_size_side;			// Border sizes for the side panels	
	
	function ur_theme() {
		//------------------------------------------------------------------	
		// Title text
		$this->title_display[0] = false;
		$this->title_display[1] = true;		
		$this->title_color[0] = "FFFFFF";
		$this->title_color[1] = "FFFFFF";
		$this->title_bold[0] = true;
		$this->title_bold[1] = true;		
		
	
		//------------------------------------------------------------------	
		// Upper title bar
		$this->tbar_size[0] = 0;					// TITLEBAR HEIGHT (CENTER PANELS)
		$this->tbar_size[1] = 26;					// TITLEBAR HEIGHT (SIDE PANELS)
		
		$this->tbar_offset[0] = 0;					// SPACE BETWEEN THE TITLE BAR AND THE PANEL (CENTER PANELS)
		$this->tbar_offset[1] = 2;					// SPACE BETWEEN THE TITLE BAR AND THE PANEL (SIDE PANELS)
		
		$this->tbar_pic[0] = "";					// TITLEBAR BACKGROUND PICTURE (CENTER PANELS)
		$this->tbar_pic[1] = "bgtitle.gif";			// TITLEBAR BACKGROUND PICTURE (SIDE PANELS)
		
	
		//------------------------------------------------------------------	
		// Panel edge size
		$this->edge_size[0][0] = 8;					// TOP
		$this->edge_size[1][0] = 4;					// LEFT
		$this->edge_size[2][0] = 4;					// RIGHT
		$this->edge_size[3][0] = 8;					// BOTTOM
		
		$this->edge_size[0][1] = 8;					// TOP
		$this->edge_size[1][1] = 4;					// LEFT
		$this->edge_size[2][1] = 4;					// RIGHT
		$this->edge_size[3][1] = 8;					// BOTTOM
		
		
		//------------------------------------------------------------------	
		// Panel edge pictures
		$this->edge_pic[0][0] = "top.gif";			// TOP
		$this->edge_pic[1][0] = "side.gif";			// LEFT
		$this->edge_pic[2][0] = "side.gif";			// RIGHT
		$this->edge_pic[3][0] = "bottom.gif";		// BOTTOM
	
		$this->edge_pic[0][1] = "top.gif";			// TOP
		$this->edge_pic[1][1] = "side.gif";			// LEFT
		$this->edge_pic[2][1] = "side.gif";			// RIGHT
		$this->edge_pic[3][1] = "bottom.gif";		// BOTTOM		
	
		
		//------------------------------------------------------------------	
		// Panel corner edges pictures
		$this->edge_pic[4][0] = "top_left.gif";		// TOP LEFT CORNER
		$this->edge_pic[5][0] = "top_right.gif";	// TOP RIGHT CORNER
		$this->edge_pic[6][0] = "bottom_left.gif";	// BOTTOM LEFT CORNER
		$this->edge_pic[7][0] = "bottom_right.gif";	// BOTTOM RIGHT CORNER
		
		$this->edge_pic[4][1] = "top_left.gif";		// TOP LEFT CORNER
		$this->edge_pic[5][1] = "top_right.gif";	// TOP RIGHT CORNER
		$this->edge_pic[6][1] = "bottom_left.gif";	// BOTTOM LEFT CORNER
		$this->edge_pic[7][1] = "bottom_right.gif";	// BOTTOM RIGHT CORNER
		
	}
}

?>
