'use strict';




(function (blocks, element, components, editor, $) {
	var el = element.createElement,
		registerBlockType = blocks.registerBlockType,
		InspectorControls = editor.InspectorControls,
		ServerSideRender = components.ServerSideRender,
		RangeControl = components.RangeControl,
		Panel = components.Panel,
		PanelBody = components.PanelBody,
		PanelRow = components.PanelRow,
		TextControl = components.TextControl,
		//NumberControl = components.NumberControl,
		TextareaControl = components.TextareaControl,
		CheckboxControl = components.CheckboxControl,
		RadioControl = components.RadioControl,
		SelectControl = components.SelectControl,
		ToggleControl = components.ToggleControl,
		//ColorPicker = components.ColorPalette,
		//ColorPicker = components.ColorPicker,
		//ColorPicker = components.ColorIndicator,
		PanelColorPicker = editor.PanelColorSettings,
		DateTimePicker = components.DateTimePicker,
		HorizontalRule = components.HorizontalRule,
		ExternalLink = components.ExternalLink;

	var MediaUpload = wp.editor.MediaUpload;

	const { Component } = wp.element;



	/*var iconCategory = el('svg', {
		width: 20,
		height: 20,
			viewBox: '0 0 16.000000 16.000000'
	},
		el('path', {
			d: 'M0 80 l0 -80 80 0 80 0 0 80 c0 73 -2 80 -19 80 -11 0 -23 -4 -26 -10 -3 -5 -17 -10 -30 -10 -13 0 -27 5 -30 10 -3 6 -17 10 -31 10 -23 0 -24 -2 -24 -80z m130 35 c0 -8 -13 -15 -32 -17 -23 -2 -36 -10 -43 -26 -5 -12 -15 -22 -22 -22 -18 0 -16 40 2 61 20 21 95 25 95 4z'
		}),
	);*/

	/*var SVG = wp.components.SVG;
	var circle = el( 'circle', { cx: 10, cy: 10, r: 10, fill: 'none', stroke: 'none', strokeWidth: '10' } );
	var svgIcon = el( SVG, { width: 20, height: 20, viewBox: '0 0 20 20'}, circle);*/
	//wp.blocks.updateCategory( 'codeboxr', { icon: iconCategory } );

	registerBlockType('codeboxr/cbxlatesttweets', {
		title: cbxlatesttweets_block.block_title,
		icon: 'twitter',
		category: cbxlatesttweets_block.block_category,
		edit: class extends Component {
			constructor(props) {
				super(...arguments);
				this.props = props;

				//this.onTitleChange = this.onTitleChange.bind(this);
				//this.updateSelectedPosts = this.updateSelectedPosts.bind(this);
			}

			componentDidMount() {
				//console.log(this.props.name, ": componentDidMount()");
			}

			componentDidUpdate(prevProps, prevState) {
				/*super.componentDidUpdate(prevProps);
				if ( this.state.response !== prevState.response ) {
					console.log('hit here')
				}*/
				//https://github.com/WordPress/gutenberg/issues/8379
				//console.log(prevProps);
				//console.log(prevState);
				//console.log(this.props.name, ": componentDidUpdate()");

				/*jQuery(".cbxlatesttweets-owl-carousel-01").owlCarousel({
					loop:true,
					margin:10,
					nav:true,
					items:1,
					autoHeight: true,
					animateOut: 'fadeOut',
					autoplay:false,
					autoplayTimeout:4000,

				});

				var nt_example1 = jQuery('.cbxlatesttweets-vertical-newsticket-01').newsTicker({
					row_height: 100,
					max_rows: 5,
					duration: 4000,
				});*/
			}


			componentWillUnmount() {
				//console.log(this.props.name, ": componentWillUnmount()");
			}


			render() {
				//const { className, attributes: { blockTitle = '' } = {} } = this.props;

				return [
					/*
					 * The ServerSideRender element uses the REST API to automatically call
					 * php_block_render() in your PHP code whenever it needs to get an updated
					 * view of the block.
					 */
					el(ServerSideRender, {
						block: 'codeboxr/cbxlatesttweets',
						attributes: this.props.attributes,
					}),

					el(InspectorControls, {},
						// 1st Panel – Form Settings
						el(PanelBody, {	title: cbxlatesttweets_block.general_settings.title,	initialOpen: true},
							el(TextControl, {
								label: cbxlatesttweets_block.general_settings.username,
								onChange: (value) => {
									this.props.setAttributes({
										username: value
									});
								},
								value: this.props.attributes.username
							}),
							el(SelectControl, {
								label: cbxlatesttweets_block.general_settings.layout,
								options: cbxlatesttweets_block.general_settings.layout_options,
								onChange: (value) => {
									this.props.setAttributes({
										layout: value
									});
								},
								value: this.props.attributes.layout
							}),
							el(TextControl, {
								label: cbxlatesttweets_block.general_settings.limit,
								onChange: (value) => {
									this.props.setAttributes({
										limit: parseInt(value)
									});
								},
								value: this.props.attributes.limit
							}),
							el( ToggleControl,
								{
									label: cbxlatesttweets_block.general_settings.include_rts,
									onChange: ( value ) => {
										this.props.setAttributes( { include_rts: value } );
									},
									checked: this.props.attributes.include_rts
								}
							),
							el( ToggleControl,
								{
									label: cbxlatesttweets_block.general_settings.exclude_replies,
									onChange: ( value ) => {
										this.props.setAttributes( { exclude_replies: value } );
									},
									checked: this.props.attributes.exclude_replies
								}
							),
							el(SelectControl, {
								label: cbxlatesttweets_block.general_settings.time_format,
								options: cbxlatesttweets_block.general_settings.time_format_options,
								onChange: (value) => {
									this.props.setAttributes({
										time_format: value
									});
								},
								value: this.props.attributes.time_format
							}),
							el(TextControl, {
								label: cbxlatesttweets_block.general_settings.date_time_format,
								onChange: (value) => {
									this.props.setAttributes({
										date_time_format: value
									});
								},
								value: this.props.attributes.date_time_format
							}),
						)
					)

				]
			}
		},

		/*
		 * In most other blocks, you'd see an 'attributes' property being defined here.
		 * We've defined attributes in the PHP, that information is automatically sent
		 * to the block editor, so we don't need to redefine it here.
		 */
		edit2: function (props) {


			return [
				/*
				 * The ServerSideRender element uses the REST API to automatically call
				 * php_block_render() in your PHP code whenever it needs to get an updated
				 * view of the block.
				 */
				el(ServerSideRender, {
					block: 'codeboxr/cbxlatesttweets',
					attributes: props.attributes,
				}),

				el(InspectorControls, {},
					// 1st Panel – Form Settings
					el(PanelBody, {	title: cbxlatesttweets_block.general_settings.title,	initialOpen: true},
						el(TextControl, {
							label: cbxlatesttweets_block.general_settings.username,
							onChange: (value) => {
								props.setAttributes({
									username: value
								});
							},
							value: props.attributes.username
						}),
						el(SelectControl, {
							label: cbxlatesttweets_block.general_settings.layout,
							options: cbxlatesttweets_block.general_settings.layout_options,
							onChange: (value) => {
								props.setAttributes({
									layout: value
								});
							},
							value: props.attributes.layout
						}),
						el(TextControl, {
							label: cbxlatesttweets_block.general_settings.limit,
							onChange: (value) => {
								props.setAttributes({
									limit: parseInt(value)
								});
							},
							value: props.attributes.limit
						}),
						el( ToggleControl,
							{
								label: cbxlatesttweets_block.general_settings.include_rts,
								onChange: ( value ) => {
									props.setAttributes( { include_rts: value } );
								},
								checked: props.attributes.include_rts
							}
						),
						el( ToggleControl,
							{
								label: cbxlatesttweets_block.general_settings.exclude_replies,
								onChange: ( value ) => {
									props.setAttributes( { exclude_replies: value } );
								},
								checked: props.attributes.exclude_replies
							}
						),
						el(SelectControl, {
							label: cbxlatesttweets_block.general_settings.time_format,
							options: cbxlatesttweets_block.general_settings.time_format_options,
							onChange: (value) => {
								props.setAttributes({
									time_format: value
								});
							},
							value: props.attributes.time_format
						}),
						el(TextControl, {
							label: cbxlatesttweets_block.general_settings.date_time_format,
							onChange: (value) => {
								props.setAttributes({
									date_time_format: value
								});
							},
							value: props.attributes.date_time_format
						}),
					)
				)

			]
		},
		// We're going to be rendering in PHP, so save() can just return null.
		save: function () {
			return null;
		},
	});
}(
	window.wp.blocks,
	window.wp.element,
	window.wp.components,
	window.wp.editor
));