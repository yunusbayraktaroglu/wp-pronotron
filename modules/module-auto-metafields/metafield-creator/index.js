/**
 * 
 * Gutenberg auto sidebar meta controller
 * 
 * @global panel_extension_options
 * 
 * Add meta controllers to gutenberg sidebar
 * with panel_extension_options send by php
 * 
 */

import './css/editor.scss';

import React, { useEffect, useState } from 'react';

import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useDispatch, useSelect } from '@wordpress/data';

import { TextControl } from '@wordpress/components';
import { ImageMeta } from './js/ImageMeta';


function MetaController({ extension }){

	/**
	 * Get custom meta
	 */
	const dispatch 	= useDispatch( 'core/editor' );
	const meta 		= useSelect(( select ) => JSON.parse( select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ extension.meta_id ] ) );
	const isSaving 	= useSelect(( select ) => select( 'core/editor' ).isSavingPost() );

	//console.log( meta );

	/**
	 * If any meta component deleted after creation, it keeps deleted data
	 * this removes deleted data in meta
	 */
	useEffect( () => {
		if ( isSaving ) {

			const revisedMeta = {};

			extension.extend.forEach( component => {
				revisedMeta[ component.id ] = meta[ component.id ];
			});

			dispatch.editPost({
				meta: {
					[ extension.meta_id ]: JSON.stringify( revisedMeta )
				}
			});

			//console.log( "saving post" );
		}
	}, [ isSaving ] );


	function editMeta( componentId, value ){

		value = value === "" ? false : value;
		
		dispatch.editPost({
			meta: {
				[ extension.meta_id ]: JSON.stringify({ ...meta, [ componentId ]: value })
			}
		});
	}

	return (
		<PluginDocumentSettingPanel 
			name={ `custom-panel-${ extension.meta_id }` }
			//icon="book"
			title={ extension.title }
			className="auto-metafields-panel">

				{ extension.extend.map( ( component, index ) => {

					if ( component.type === "image" ){
						return (
							<ImageMeta key={ index } editMeta={ editMeta } component={ component } value={ meta[ component.id ] } />
						)
					}

					if ( component.type === "string" ){
						return (
							<TextControl
								key={ index }
								label={ component.id }
								value={ meta[ component.id ] || "" }
								onChange={ ( value ) => editMeta( component.id, value ) }
							/>
						)
					}
					
				} )}

		</PluginDocumentSettingPanel>
	)
}



function PanelExtensionPlugin(){

	const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType() );
	const [ extensions, setExtensions ] = useState( [] );
	
	useEffect( () => {
		const filteredExtensions = window.panel_extension_options.filter( option => {
			if ( option.post_type ){
				return option.post_type === postType;
			} else {
				return true;
			}
		});
		//console.log( filteredExtensions, postType );
		setExtensions( filteredExtensions );
	}, [] );

	if ( ! extensions.length ) return null;

	/**
	 * There are some extensions for post type
	 * Start render plugin
	 */
	const customPanels = extensions.map(( extension, index ) => (
		<MetaController key={ index } extension={ extension } />
	));

	return <>{ customPanels }</>
}


registerPlugin( 'wp-pronotron-custom-meta-controller', { 
	render: PanelExtensionPlugin 
} );