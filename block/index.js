
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { login as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import metadata from './block.json';
metadata.icon = icon;

registerBlockType( 
	metadata.name, metadata
);