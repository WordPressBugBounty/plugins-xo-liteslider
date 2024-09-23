<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * XO Slider Template.
 *
 * @version 2.2.3
 *
 * If you need to edit this file, copy this template.php and style.css files to the following directory
 * in the theme. The files in the theme take precedence.
 *
 * Directory: "(Theme directory)/xo-liteslider/templates/(Template ID)/"
 */
class XO_Slider_Template_Thumbnail extends XO_Slider_Template_Base {
	/**
	 * Template ID.
	 *
	 * @var string
	 */
	public $id = 'thumbnail';

	/**
	 * Template name.
	 *
	 * @var string
	 */
	public $name = 'Thumbnail';

	/**
	 * Template version.
	 *
	 * @var string
	 */
	public $version = '2.2.3';

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $template_slug Template slug.
	 */
	public function enqueue_scripts( $template_slug ) {
		if ( $this->id === $template_slug ) {
			wp_enqueue_style( "xo-slider-template-{$this->id}", $this->get_template_uri() . 'style.css', array(), $this->version );
		}
	}

	/**
	 * Gets the description HTML.
	 */
	public function get_description() {
		return __( 'A template that displays a slider with thumbnails. Supports videos.', 'xo-liteslider' );
	}

	/**
	 * Get the slider HTML.
	 *
	 * @param object $slide {
	 *     Slide data.
	 *
	 *     @type int   $id     Slide ID.
	 *     @type array $slides Slides data.
	 *     @type array $params Slide parameters.
	 * }
	 * @return string Slider HTML.
	 */
	public function get_html( $slide ) {
		$style = '';
		if ( ! empty( $slide->params['width'] ) ) {
			$style .= sprintf( 'width:%dpx;', $slide->params['width'] );
		}
		if ( ! empty( $slide->params['height'] ) ) {
			$style .= sprintf( 'height:%dpx;', $slide->params['height'] );
		}

		$html  = '<div class="swiper swiper-container gallery-main" style="' . $style . '">' . "\n";
		$html .= '<div class="swiper-wrapper">' . "\n";

		$thumbs_image_width  = (int) empty( $slide->params['thumbs_image_width'] ) ? 0 : $slide->params['thumbs_image_width'];
		$thumbs_image_height = (int) empty( $slide->params['thumbs_image_height'] ) ? 0 : $slide->params['thumbs_image_height'];

		$thumbs = array();
		foreach ( $slide->slides as $slide_data ) {
			$thumb = '';

			if ( isset( $slide_data['media_type'] ) && in_array( $slide_data['media_type'], array( 'image', 'video', 'youtube' ), true ) ) {
				$mime_type_class = " mime-type-{$slide_data['media_type']}";
			} else {
				$mime_type_class = '';
			}

			$html .= "<div class=\"swiper-slide{$mime_type_class}\">";

			if ( 'image' === $slide_data['media_type'] ) {
				$attr = array( 'class' => 'slide-image' );
				if ( isset( $slide_data['alt'] ) ) {
					$attr['alt'] = $slide_data['alt'];
				}
				if ( isset( $slide_data['title_attr'] ) ) {
					$attr['title'] = $slide_data['title_attr'];
				}
				$img = wp_get_attachment_image( $slide_data['media_id'], 'full', false, $attr );
				if ( $img ) {
					$html .= '<div class="slide-image">';
					if ( ! empty( $slide_data['link'] ) ) {
						$target = ( ! empty( $slide_data['link_newwin'] ) && $slide_data['link_newwin'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
						$html  .= '<a href="' . esc_url( $slide_data['link'] ) . '"' . $target . '>' . $img . '</a>';
					} else {
						$html .= $img;
					}
					$html .= '</div>';

					if ( ! empty( $slide->params['content'] ) ) {
						$html .= '<div class="slide-content">' . "\n";

						if ( ! empty( $slide_data['title'] ) ) {
							$html .= '<div class="slide-content-title">' . wp_kses_post( $slide_data['title'] ) . '</div>' . "\n";
						}
						if ( ! empty( $slide_data['subtitle'] ) ) {
							$html .= '<div class="slide-content-subtitle">' . wp_kses_post( $slide_data['subtitle'] ) . '</div>' . "\n";
						}
						if ( ! empty( $slide_data['content'] ) ) {
							$html .= '<div class="slide-content-text">' . wp_kses_post( $slide_data['content'] ) . '</div>' . "\n";
						}
						if ( ! empty( $slide_data['button_text'] ) && ! empty( $slide_data['button_link'] ) ) {
							$target = ( ! empty( $slide_data['button_newwin'] ) && $slide_data['button_newwin'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
							$html  .= '<div class="slide-content-button"><a href="' . esc_url( $slide_data['button_link'] ) . '" class="slide-content-button-main"' . $target . '>' . wp_kses_post( $slide_data['button_text'] ) . '</a></div>' . "\n";
						}

						$html .= "</div>\n"; // <!-- .slide-content -->
					}
				}

				if ( 0 === $thumbs_image_width || 0 === $thumbs_image_height ) {
					$thumb = $img;
				} else {
					$image_src = wp_get_attachment_image_src( $slide_data['media_id'], array( $thumbs_image_width, $thumbs_image_height ), false );
					if ( false !== $image_src ) {
						$thumb = '<img src="' . esc_url( $image_src[0] ) . '" alt="" width="' . $thumbs_image_width . '" height="' . $thumbs_image_height . '">';
					}
				}
			} elseif ( 'video' === $slide_data['media_type'] ) {
				$html .= $this->get_attachment_video( $slide_data );

				if ( 0 === $thumbs_image_width || 0 === $thumbs_image_height ) {
					$thumb = get_the_post_thumbnail( $slide_data['media_id'] );
				} else {
					$thumbnail_id = get_post_thumbnail_id( $slide_data['media_id'] );
					if ( $thumbnail_id ) {
						$image_src = wp_get_attachment_image_src( $thumbnail_id, array( $thumbs_image_width, $thumbs_image_height ), true );
						if ( false !== $image_src ) {
							$thumb = '<img src="' . esc_url( $image_src[0] ) . '" alt="" width="' . $thumbs_image_width . '" height="' . $thumbs_image_height . '">';
						}
					}
				}
			} elseif ( 'youtube' === $slide_data['media_type'] ) {
				$html .= '<div class="youtube-wrap">' . $this->get_embed_youtube( $slide_data ) . '</div>';

				if ( 1 === preg_match( '/[\/?=]([a-zA-Z0-9_\-]{11})[&\?]?/', $slide_data['media_link'], $matches ) ) {
					if ( $thumbs_image_height <= 90 ) {
						$filename = 'default';
					} elseif ( $thumbs_image_height <= 180 ) {
						$filename = 'mqdefault';
					} elseif ( $thumbs_image_height <= 360 ) {
						$filename = 'hqdefault';
					} elseif ( $thumbs_image_height <= 480 ) {
						$filename = 'sddefault';
					} else {
						$filename = 'maxresdefault.jpg';
					}
					$thumb = '<img src="https://img.youtube.com/vi/' . $matches[1] . '/' . $filename . '.jpg" alt="" />';
				}
			}

			$html .= '</div>' . "\n"; // <!-- .swiper-slide -->

			if ( empty( $thumb ) ) {
				$thumb = '<img src="' . XO_SLIDER_URL . '/img/no-image.png" alt="No image">';
			}

			$thumbs[] = $thumb;
		}

		$html .= '</div>' . "\n"; // <!-- .swiper-wrapper -->

		if ( isset( $slide->params['pagination'] ) && $slide->params['pagination'] ) {
			$html .= '<div class="swiper-pagination swiper-pagination-white"></div>' . "\n";
		}

		if ( isset( $slide->params['navigation'] ) && $slide->params['navigation'] ) {
			$html .= '<div class="swiper-button-next swiper-button-white"></div>' . "\n";
			$html .= '<div class="swiper-button-prev swiper-button-white"></div>' . "\n";
		}

		$html .= '</div>' . "\n"; // <!-- swiper-container -->

		$thumbs_style = '';
		if ( ! empty( $slide->params['thumbs_width'] ) ) {
			$thumbs_style .= "max-width:{$slide->params['thumbs_width']}px;";
		}
		if ( ! empty( $slide->params['thumbs_height'] ) ) {
			$thumbs_style .= "height:{$slide->params['thumbs_height']}px;";
		}
		if ( ! empty( $slide->params['thumbs_margin_top'] ) ) {
			$thumbs_style .= "margin-top:{$slide->params['thumbs_margin_top']}px;";
		}
		if ( $thumbs_style ) {
			$thumbs_style = " style=\"{$thumbs_style}\"";
		}

		$html .= '<div class="swiper swiper-container gallery-thumbs"' . $thumbs_style . ">\n";
		$html .= '<div class="swiper-wrapper">' . "\n";
		foreach ( $thumbs as $thumb ) {
			$style = '';
			if ( 0 < $thumbs_image_width && 0 < $thumbs_image_height ) {
				$style = ' style="width:' . $thumbs_image_width . 'px; height:' . $thumbs_image_height . 'px;"';
			}
			$html .= '<div class="swiper-slide"' . $style . '>' . $thumb . '</div>' . "\n";
		}

		$html .= "</div>\n"; // <!-- .swiper-wrapper -->

		if ( isset( $slide->params['thumbs_navigation'] ) && $slide->params['thumbs_navigation'] ) {
			$html .= '<div class="swiper-button-next swiper-button-white"></div>' . "\n";
			$html .= '<div class="swiper-button-prev swiper-button-white"></div>' . "\n";
		}

		$html .= "</div>\n"; // <!-- .swiper-container -->

		return $html;
	}

	/**
	 * Get the slider script.
	 *
	 * @param object $slide {
	 *     Slide data.
	 *
	 *     @type int   $id     Slide ID.
	 *     @type array $slides Slides data.
	 *     @type array $params Slide parameters.
	 * }
	 * @return array|false Slider script, false to not output the script.
	 */
	public function get_script( $slide ) {
		if ( ! empty( $slide->params['thumbs_per_view'] ) ) {
			$slides_per_view = (int) $slide->params['thumbs_per_view'];
		} elseif ( isset( $slide->params['thumbs_per_view_type'] ) && 'auto' === $slide->params['thumbs_per_view_type'] ) {
			$slides_per_view = 'auto';
		} else {
			$slides_per_view = count( $slide->slides );
		}

		$slide_id = (int) $slide->id;

		$thumb_params = array(
			'slidesPerView'         => $slides_per_view,
			'freeMode'              => true,
			'watchSlidesVisibility' => true,
			'watchSlidesProgress'   => true,
		);

		if ( isset( $slide->thumb_params['thumbs_navigation'] ) ) {
			$thumb_params['thumbs_navigation'] = array(
				'nextEl' => '.swiper-button-next',
				'prevEl' => '.swiper-button-prev',
			);
		}

		if ( ! empty( $slide->params['thumbs_space_between'] ) ) {
			$thumb_params['spaceBetween'] = $slide->params['thumbs_space_between'];
		}

		$params = array();

		if ( isset( $slide->params['pagination'] ) ) {
			$params['pagination'] = array(
				'el'        => "#xo-slider-{$slide_id} .swiper-pagination",
				'clickable' => true,
			);
		}

		if ( isset( $slide->params['navigation'] ) ) {
			$params['navigation'] = array(
				'nextEl' => '.swiper-button-next',
				'prevEl' => '.swiper-button-prev',
			);
		}

		$params['speed']          = (int) ( $slide->params['speed'] ?? 600 );
		$params['scrollbar']      = array( 'hide' => true );
		$params['loop']           = (bool) ( $slide->params['loop'] ?? true );
		$params['centeredSlides'] = (bool) ( $slide->params['centered_slides'] ?? false );

		if ( isset( $slide->params['effect'] ) && in_array( $slide->params['effect'], array( 'slide', 'fade', 'cube', 'coverflow', 'flip', 'cards', 'creative' ), true ) ) {
			$params['effect'] = $slide->params['effect'];
		}

		if ( ! empty( $slide->params['autoplay'] ) ) {
			$params['autoplay'] = array(
				'delay'                => (int) ( $slide->params['delay'] ?? 3000 ),
				'stopOnLastSlide'      => (bool) ( $slide->params['stop_on_last_slide'] ?? false ),
				'disableOnInteraction' => (bool) ( $slide->params['disable_on_interaction'] ?? true ),
			);
		} else {
			$params['autoplay'] = false;
		}

		if ( isset( $slide->params['slides_per_view'] ) && ! empty( $slide->params['slides_per_view'] ) ) {
			$params['slidesPerView'] = (float) $slide->params['slides_per_view'];
		}

		if ( isset( $slide->params['auto_height'] ) && ! empty( $slide->params['auto_height'] ) ) {
			$params['autoHeight'] = (bool) $slide->params['auto_height'];
		}

		if ( isset( $slide->params['slides_per_group'] ) && ! empty( $slide->params['slides_per_group'] ) ) {
			$params['slidesPerGroup'] = (int) $slide->params['slides_per_group'];
		}

		if ( isset( $slide->params['space_between'] ) && ! empty( $slide->params['space_between'] ) ) {
			$params['spaceBetween'] = (int) $slide->params['space_between'];
		}

		/**
		 * Filter slider script parameters.
		 *
		 * @since 2.0.0
		 *
		 * @param string      $params Script parameters.
		 * @param array       $slide  Slide data.
		 * @param string|null $key    Script parameter key.
		 */
		$thumb_params = apply_filters( 'xo_slider_script_parameter', $thumb_params, $slide, 'thumbs' );
		$params       = apply_filters( 'xo_slider_script_parameter', $params, $slide, '' );

		$thumb_json = wp_json_encode( $thumb_params );
		$json       = substr( substr( wp_json_encode( $params ), 1 ), 0, -1 );

		$script =
			"var xoSlider{$slide_id}Thumbs = new Swiper('#xo-slider-{$slide_id} .gallery-thumbs', {$thumb_json});" .
			"var xoSlider{$slide_id} = new Swiper('#xo-slider-{$slide_id} .gallery-main', {{$json}, thumbs: {swiper: xoSlider{$slide_id}Thumbs}});";

		$script .= <<<SCRIPT
(function() {
	const videos = document.querySelectorAll('#xo-slider-{$slide_id} video');
	videos.forEach((video) => {
		video.autoplay = false;
	});
	var activeVideo = document.querySelector('#xo-slider-{$slide_id} .swiper-container .swiper-slide-active video');
	if ( activeVideo !== null ) {
		activeVideo.autoplay = true;
	}
}());

xoSlider{$slide_id}.on( 'transitionStart', () => {
	var videos = document.querySelectorAll('#xo-slider-{$slide_id} .swiper-container video');
	videos.forEach((video) => {
		video.pause();
	});
});

xoSlider{$slide_id}.on( 'slideChangeTransitionEnd', () => {
	var activeVideo = document.querySelector('#xo-slider-{$slide_id} .swiper-container .swiper-slide-active video');
	if ( activeVideo !== null ) {
		activeVideo.play();
	}
} );
SCRIPT;

		return $script;
	}
}
