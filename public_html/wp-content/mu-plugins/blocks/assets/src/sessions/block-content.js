/**
 * External dependencies
 */
import { get } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { Component } = wp.element;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import {ItemTitle, ItemHTMLContent, ItemPermalink} from '../shared/block-content';
import { tokenSplit, arrayTokenReplace, intersperse, listify } from '../shared/i18n';
import GridContentLayout from '../shared/grid-layout/block-content';
import FeaturedImage from '../shared/featured-image';
import { ICON } from './index';

function SessionSpeakers( { session } ) {
	let speakerData = get( session, '_embedded.speakers', [] );

	speakerData = speakerData.map( ( speaker ) => {
		const { link = '' } = speaker;
		let {  title = {} } = speaker;

		title = title.rendered.trim() || __( 'Unnamed', 'wordcamporg' );

		if ( ! link ) {
			return title;
		}

		return (
			<a
				key={ link }
				href={ link }
			>
				{ title }
			</a>
		);
	} );

	const speakers = arrayTokenReplace(
		/* translators: %s is a list of names. */
		tokenSplit( __( 'Presented by %s', 'wordcamporg' ) ),
		[ listify( speakerData ) ]
	);

	return (
		<div className="wordcamp-item-meta wordcamp-session-speakers">
			{ speakers }
		</div>
	);
}

function SessionMeta( { session } ) {
	let metaContent;
	const terms = get( session, '_embedded[\'wp:term\']', [] ).flat();

	if ( session.session_track.length ) {
		const [ firstTrack ] = terms.filter( ( term ) => {
			return 'wcb_track' === term.taxonomy;
		} );

		metaContent = arrayTokenReplace(
			/* translators: 1: A date; 2: A time; 3: A location; */
			tokenSplit( __( '%1$s at %2$s in %3$s', 'wordcamporg' ) ),
			[
				session.session_date_time.date,
				session.session_date_time.time,
				(
					<span
						key={ firstTrack.id }
						className={ classnames( 'wordcamp-session-track', 'wordcamp-session-track-' + firstTrack.slug.trim() ) }
					>
						{ firstTrack.name.trim() }
					</span>
				),
			]
		);
	} else {
		metaContent = arrayTokenReplace(
			/* translators: 1: A date; 2: A time; */
			tokenSplit( __( '%1$s at %2$s', 'wordcamporg' ) ),
			[
				session.session_date_time.date,
				session.session_date_time.time,
			]
		);
	}

	return (
		<div className="wordcamp-session-time-location">
			{ metaContent }
		</div>
	);
}

function SessionCategory( { session } ) {
	let categoryContent;
	const terms = get( session, '_embedded[\'wp:term\']', [] ).flat();

	if ( session.session_category.length ) {
		/* translators: used between list items, there is a space after the comma */
		const separator = __( ', ', 'wordcamporg' );
		const categories = terms
			.filter( ( term ) => {
				return 'wcb_session_category' === term.taxonomy;
			} )
			.map( ( term ) => {
				return (
					<span
						key={ term.slug }
						className={ classnames( 'wordcamp-session-category', 'wordcamp-session-category-' + term.slug ) }
					>
						{ term.name.trim() }
					</span>
				);
			} );

		categoryContent = intersperse( categories, separator );
	}

	return (
		<div className="wordcamp-session-categories">
			{ categoryContent }
		</div>
	);
}

class SessionsBlockContent extends Component {
	hasSpeaker( session ) {
		return get( session, '_embedded.speakers', [] ).length > 0;
	}

	render() {
		const { attributes, sessionPosts } = this.props;
		const { show_speaker, show_images, image_align, featured_image_width, content, show_meta, show_category } = attributes;

		return (
			<GridContentLayout
				className="wordcamp-sessions-block"
				{ ...this.props }
			>
				{ sessionPosts.map( ( post ) =>
					<div
						key={ post.slug }
						className={ classnames(
							'wordcamp-block-post-list-item',
							'wordcamp-session',
							'wordcamp-session-' + post.slug,
							'wordcamp-clearfix'
						) }
					>
						<ItemTitle
							className="wordcamp-session-title"
							headingLevel={ 3 }
							title={ post.title.rendered.trim() }
							link={ post.link }
						/>

						{ show_speaker && this.hasSpeaker( post ) &&
							<SessionSpeakers session={ post } />
						}

						{ show_images &&
							<FeaturedImage
								className={ classnames( 'wordcamp-session-image-container', 'align-' + image_align ) }
								wpMediaDetails={ get( post, '_embedded.wp:featuredmedia[0].media_details.sizes', {} ) }
								alt={ post.title.rendered.trim() }
								width={ Number( featured_image_width ) }
								imageLink={ post.link }
								fallbackIcon={ ICON }
							/>
						}

						{ 'none' !== content &&
							<ItemHTMLContent
								className={ classnames( 'wordcamp-session-content-' + content ) }
								content={ 'full' === content ? post.content.rendered.trim() : post.excerpt.rendered.trim() }
							/>
						}

						{ ( show_meta || show_category ) &&
							<div className="wordcamp-item-meta wordcamp-session-details">
								{ show_meta &&
									<SessionMeta session={ post } />
								}
								{ show_category &&
									<SessionCategory session={ post } />
								}
							</div>
						}

						{ ( 'full' === content ) &&
							<ItemPermalink
								link={ post.link }
								linkText={ __( 'Visit session page', 'wordcamporg' ) }
							/>
						}
					</div>
				) }
			</GridContentLayout>
		);
	}
}

export default SessionsBlockContent;
