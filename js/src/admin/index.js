import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import Switch from 'flarum/common/components/Switch';

app.initializers.add('nippytime/post-pin-flarum', () => {
  app.extensionData
    .for('nippytime-post-pin-flarum')
    .registerSetting({
      setting: 'nippytime-post-pin.allow_self_pin',
      type: 'switch',
      label: app.translator.trans('nippytime-post-pin.admin.settings.allow_self_pin_label'),
      help: app.translator.trans('nippytime-post-pin.admin.settings.allow_self_pin_help'),
    })
    .registerSetting({
      setting: 'nippytime-post-pin.allow_any_post',
      type: 'switch', 
      label: app.translator.trans('nippytime-post-pin.admin.settings.allow_any_post_label'),
      help: app.translator.trans('nippytime-post-pin.admin.settings.allow_any_post_help'),
    })
    .registerPermission(
      {
        icon: 'fas fa-thumbtack',
        label: app.translator.trans('nippytime-post-pin.admin.permissions.pin_posts_label'),
        permission: 'discussion.pinPost',
        allowGuest: false,
      },
      'moderate',
      95
    );
});