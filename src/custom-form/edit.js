/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */

const CustomFormEdit = () => {
    return (
        <div>
            <h3>{__('Submit your feedback', 'custom-form')}</h3>
            <form>
                <input type="text" placeholder={__('First Name', 'custom-form')} />
                <input type="text" placeholder={__('Last Name', 'custom-form')} />
                <input type="email" placeholder={__('Email', 'custom-form')} />
                <input type="text" placeholder={__('Subject', 'custom-form')} />
                <textarea placeholder={__('Message', 'custom-form')}></textarea>
                <button type="submit" disabled>{__('Submit (Preview Only)', 'custom-form')}</button>
            </form>
        </div>
    );
};

export default CustomFormEdit;
