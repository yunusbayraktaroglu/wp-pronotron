/**
 * Extend "core/gallery" block
 * 
 * - Add "isCarousel" attribute to default attributes
 * - Add "data-carousel" to html output if isCarousel

 * References
 * @see https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/
 * @see https://gutenberg.10up.com/guides/modifying-the-markup-of-a-core-block
 * 
 */

//import './css/editor.scss';
//import './css/style.scss';

import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';



/** 
 * - Define an attribute named "isCarousel" for core/gallery blocks
 * 	 can to be setted with setAttributes()
 */
function modifyGalleryBlockOptions( settings, name ) {

	if ( name !== 'core/gallery' ) {
		return settings;
	}

	return {
		...settings,
        attributes: {
            ...settings.attributes,
			isCarousel: {
				type: "boolean",
				default: false
			}
        }
	};
}

addFilter(
    'blocks.registerBlockType',
    'wp-pronotron/modify-gallery-block-options',
    modifyGalleryBlockOptions
);



/**
 * - Extend "core/gallery" block's edit() function
 */
const CustomizedCoreGalleryBlock = createHigherOrderComponent( ( BlockEdit ) => ( props ) => {


	/**
	 * Pick "core/image" blocks and control image's validity
	 */
    const { name, attributes, setAttributes } = props;

    if ( name !== 'core/gallery' ){
        return <BlockEdit { ...props } />;
    }

    const { isCarousel } = attributes;

    return (
        <>
		    <InspectorControls>
                <PanelBody>
					<ToggleControl
						label={ __( 'Create carousel' ) }
						checked={ !! isCarousel }
                        onChange={() => {
                            setAttributes({ isCarousel: ! isCarousel });
                        }}
						help={ 'Create image carousel with gallery.' }
					/>
                </PanelBody>
            </InspectorControls>

			<BlockEdit { ...props } />
        </>
    );

}, 'withSidebarSelect' );

addFilter(
    'editor.BlockEdit',
    'wp-pronotron/customized-core-gallery',
    CustomizedCoreGalleryBlock
);



/** 
 * Add "data-carousel" to html output
 */
function CoreGalleryAddClassIfCarousel( props, blockType, attributes ) {

    if ( blockType.name === 'core/gallery' && attributes.isCarousel ){
		return {
            ...props,
            "data-carousel": "default",
        };
    }

	return props;
}

addFilter(
    'blocks.getSaveContent.extraProps',
    'wp-pronotron/core-gallery-carousel-data-attribute',
    CoreGalleryAddClassIfCarousel
);