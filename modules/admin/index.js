import './css/editor.scss';
import './css/style.scss';

import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import { createRoot } from '@wordpress/element';


function greatestCommonDivisor( width, height ){
	return ( width % height ) ? greatestCommonDivisor( height, width % height ) : height;
}


function UploadSizeControl({ name, uploadSize, setUploadSize }){

	const [ ratioInfo, setRatioInfo ] = useState( '' );

	useEffect(() => {
		const divisor = greatestCommonDivisor( uploadSize.width, uploadSize.height );
		const ratio = {
			x: uploadSize.width / divisor,
			y: uploadSize.height / divisor
		};
		setRatioInfo( `( X Ratio: ${ ratio.x }, Y Ratio: ${ ratio.y } )` );
	}, [ uploadSize ] );

	const changeSize = (event) => {
		setUploadSize({
			...uploadSize,
			[event.target.dataset.prop]: parseInt( event.target.value ),
		});
	};

	return (
		<fieldset className="flex-middle">
			<fieldset>
				<legend className="screen-reader-text"><span>Force image sizes on upload.</span></legend>
				<label htmlFor={ `${ name }[width]` }>Width :</label>
				<input 
					onChange={ changeSize }
					type="number"
					className="small-text"
					data-prop="width"
					id={ `${ name }[width]` }
					name={ `${ name }[width]` }
					defaultValue={ uploadSize.width }
					min="500"
					step="100" />
				<br />
				<label htmlFor={ `${ name }[height]` }>Height :</label>
				<input 
					onChange={ changeSize }
					type="number"
					className="small-text"
					data-prop="height"
					id={ `${ name }[height]` }
					name={ `${ name }[height]` }
					defaultValue={ uploadSize.height }
					min="500"
					step="100" />
				<br />
			</fieldset>
			<span className="helper">{ ratioInfo }</span>
		</fieldset>
	)
}


function RatioControl({ type, name, defaultRatio, uploadSize }){

	const [ ratio, setRatio ] = useState( defaultRatio );
	const [ sizeInfo, setSizeInfo ] = useState( '' );
	const [ active, setActive ] = useState( true );

	useEffect(() => {

		if ( ! active ) return;

		let rationized;

		if ( type === "portrait" ){
			rationized = {
				width: uploadSize.height / ratio.y * ratio.x,
				height: uploadSize.height
			};
		} else {
			rationized = {
				width: uploadSize.width,
				height: uploadSize.width / ratio.x * ratio.y
			};
		}

		if ( ! Number.isInteger( rationized.width ) || ! Number.isInteger( rationized.height ) ){
			setSizeInfo( `( ERROR: Width: ${ rationized.width }, Height: ${ rationized.height } )` );
		} else {
			setSizeInfo( `( Width: ${ rationized.width }, Height: ${ rationized.height } )` );
		}

	}, [ ratio, uploadSize ] );


	const changeRatio = ( event ) => {
		const temporary = { ...ratio, [event.target.dataset.prop]: parseInt( event.target.value ) };

		if ( (type === "portrait" && temporary.x >= temporary.y) || (type === "landscape" && temporary.y >= temporary.x) ){
			event.preventDefault();
			return false;
		}
		setRatio( temporary );
	};

	if ( ! active ) return null;

	return (
		<fieldset>
			<button 
				type="button" 
				className="button button-remove" 
				onClick={ () => setActive(false) }>X</button>
			<fieldset>
				<label htmlFor={ `${ name }[x]` }>X Ratio :</label>
				<input 
					onChange={ changeRatio }
					value={ ratio.x }
					type="number"
					className="small-text"
					id={ `${ name }[x]` }
					name={ `${ name }[x]` }
					min="1"
					step="1"
					data-prop="x"
				/>
				<br />
				<label htmlFor={ `${ name }[y]` }>Y Ratio :</label>
				<input 
					onChange={ changeRatio }
					value={ ratio.y }
					type="number"
					className="small-text"
					id={ `${ name }[y]` }
					name={ `${ name }[y]` }
					min="1"
					step="1"
					data-prop="y"
				/>
				<br/>
			</fieldset>
			<span className="helper">{ sizeInfo }</span>
		</fieldset>
	)
}




function ImageControls({ optionId, defaultSettings }){

	//console.log( optionId, defaultSettings );

	const [ uploadSize, setUploadSize ] = useState( defaultSettings['upload_sizes'] );
	const [ landscapeRatios, setLandscapeRatios ] = useState( defaultSettings['landscape_ratios'] );
	const [ portraitRatios, setPortraitratios ] = useState( defaultSettings['portrait_ratios'] );

	return (
		<>
			<UploadSizeControl 
				name={ `${ optionId }[upload_sizes]` }
				uploadSize={ uploadSize } 
				setUploadSize={ setUploadSize } />

			<div className="fieldset-wrapper">
				<div className="flex-middle">
					<h4>Landscape Ratios</h4>
					<button 
						type="button" 
						className="button button-add" 
						onClick={ () => setLandscapeRatios([ ...landscapeRatios, { x: 5, y: 3 } ]) }
					>
							Add Ratio
					</button>
				</div>
				<div className="fieldset-list">
					{ landscapeRatios.map( ( ratio, index ) =>
						<RatioControl 
							key={ index } 
							name={ `${ optionId }[landscape_ratios][${ index }]` }
							type="landscape"
							defaultRatio={ ratio } 
							uploadSize={ uploadSize } />
					) }
				</div>
			</div>

			<div className="fieldset-wrapper">
				<div className="flex-middle">
					<h4>Portrait Ratios</h4>
					<button 
						type="button" 
						className="button button-add" 
						onClick={ () => setPortraitratios([ ...portraitRatios, { x: 3, y: 5 } ]) }
					>
							Add Ratio
					</button>
				</div>
				<div className="fieldset-list">
					{ portraitRatios.map( (ratio, index) =>
						<RatioControl 
							key={ index } 
							name={ `${ optionId }[portrait_ratios][${ index }]` }
							type="portrait"
							defaultRatio={ ratio } 
							uploadSize={ uploadSize } />
					) }
				</div>
			</div>
		</>
	)
}



const root = document.getElementById( 'pronotron-module-settings' );
const defaultSettings = JSON.parse( root.dataset.default );
const optionId = JSON.parse( root.dataset.option );
const reactRoot = createRoot( root );

document.addEventListener( "DOMContentLoaded", function(event) {
	reactRoot.render( <ImageControls optionId={ optionId } defaultSettings={ defaultSettings } /> )
});