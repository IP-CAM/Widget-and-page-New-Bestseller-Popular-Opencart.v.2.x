<?php
	function form_help( $element, $label = '', $parent_name = '' )
	{
		$html = '';

		if ( $label ) {
			$html .= '<label class="col-sm-2 control-label">' . $label . '</label>';
		}

		if ( $parent_name ) {
			$element['name'] = $parent_name . '[' . $element['name'] . ']';
		}

		$props = '';

		if ( !empty( $element['props'] ) ) {
			foreach( $element['props'] as $v ) {
				$props .= $v['name'] . '="' . $v['value'] . '"';
			}
		}

		switch( $element['field'] ) {

			case 'input':
				$html .= '<div class="col-sm-10"><input class="form-control ' . ( isset( $element['class'] ) ? $element['class'] : '' ) . '" type="' . $element['type'] . '" name="' . $element['name'] . '" value="' . $element['value'] . '" ' . $props . '></div>';
				break;

			case 'select':
				$html .= '<div class="col-sm-10"><select class="form-control ' . ( isset( $element['class'] ) ? $element['class'] : '' ) . '" name="' . $element['name'] . '">';
				foreach( $element['options'] as $option ) {
					if ( $element['value'] == $option['value'] ) {
						$html .= '<option value="' . $option['value'] . '" selected>' . $option['name'] . '</option>';
					} else {
						$html .= '<option value="' . $option['value'] . '">' . $option['name'] . '</option>';
					}
				}
				$html .= '</select></div>';
				break;

			case 'textarea':
				$html .= '<div class="col-sm-10"><textarea class="form-control ' . ( isset( $element['class'] ) ? $element['class'] : '' ) . '" rows="6" name="' . $element['name'] . '">' . $element['value'] . '</textarea></div>';
				break;

		}

		if ( $html ) {
			$html = '<div class="form-group">' . $html . '</div>';
		}

		return $html;
	}

?>