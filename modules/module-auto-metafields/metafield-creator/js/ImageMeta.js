import { __ } from '@wordpress/i18n';
import { Button, BaseControl, ResponsiveWrapper } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';


export function ImageMeta({ component, value, editMeta }){

	/**
	 * (1) Value is "" string in new posts
	 * (2) Value is undefined if a new component added to custom meta after custom meta created
	 */
	//console.log( component, value );
	
	return (
		<BaseControl label={ component.id } >

			<MediaUploadCheck>
				<MediaUpload
					onSelect={( media ) => {
						//console.log( 'selected ', media )
						editMeta( component.id, media.sizes.thumbnail.url );
					}}
					value={ value }
					allowedTypes={ [ 'image' ] }
					render={ ({ open }) => (
						<div className="editor-post-application-icon__container">
							<Button 
								className={ ! value ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview'}
								onClick={ open }
							>
								{ ! value && __( 'Choose an app icon', 'pronotron' ) }
								{ value && (
									<ResponsiveWrapper
										naturalWidth={ 150 }
										naturalHeight={ 150 }
										isInline
									>
										<img src={ value } style={{ width: "auto" }} />
									</ResponsiveWrapper>
								)}
							</Button>
						</div>
					) }
				/>
			</MediaUploadCheck>

			{/* { value && 
				<MediaUploadCheck>
					<MediaUpload
						title={ __( 'Replace icon', 'pronotron' ) }
						value={ value }
						onSelect={( media ) => {
							console.log( 'selected ', media )
							editMeta( component.id, media.sizes.thumbnail.url );
						}}
						allowedTypes={ [ 'image' ] }
						render={ ({ open }) => (
							<Button
								variant='secondary'
								onClick={ open }>
									{ __( 'Replace icon', 'awp' ) }
							</Button>
						)}
					/>
				</MediaUploadCheck>
			} */}

			{ value && 
				<MediaUploadCheck>
					<Button 
						onClick={ () => editMeta( component.id, false ) }
						variant="link"
						isDestructive
					>
						{ __( 'Remove icon', 'awp' ) }
					</Button>
				</MediaUploadCheck>
			}

		</BaseControl>
	)
}