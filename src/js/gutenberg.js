import { PluginPostStatusInfo } from '@wordpress/editor';
import { ToggleControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

const LockLastModifiedDatePanel = () => {
    const { meta, lastModified } = useSelect(
        select => ({
        meta: select('core/editor').getEditedPostAttribute('meta'),
        lastModified: select('core/editor').getEditedPostAttribute('modified')
        }),
        []
    );

    const { editPost } = useDispatch('core/editor');
    const metaKey = window?.nextfly_llmd_data?.metaKey;
    const isLocked = Boolean(meta?.[metaKey]);

    const handleToggle = (value) => {
        editPost({
            meta: {
                [metaKey]: Boolean(value)
            }
        });
    };

    if (!metaKey) {
        return null;
    }

    return (
        <PluginPostStatusInfo
            className = "lock-last-modified-date-panel"
        >
            <div className = "lock-modified-date-info" >
                <span> {__('Last modified on:', 'lock-last-modified-date')} </span>
                <strong> {lastModified} </strong>
            </div>
            <ToggleControl
                label = {__('Modified Date', 'lock-last-modified-date')}
                help = {isLocked ? __('Date is locked', 'lock-last-modified-date') : __('Date will update', 'lock-last-modified-date')}
                checked = {isLocked}
                onChange = {handleToggle}
                __nextHasNoMarginBottom = {true}
            />
        </PluginPostStatusInfo>
    );
};

registerPlugin('nextfly-llmd-lock-last-modified-date', {
    render: LockLastModifiedDatePanel,
    icon: 'lock'
});
