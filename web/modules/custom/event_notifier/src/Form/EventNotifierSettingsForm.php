<?php

namespace Drupal\event_notifier\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Event Notifier settings for this site.
 */
class EventNotifierSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'event_notifier_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['event_notifier.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('event_notifier.settings');

    $form['notification_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Notification Email'),
      '#description' => $this->t('Email address to receive event notifications.'),
      '#default_value' => $config->get('notification_email'),
      '#required' => TRUE,
    ];

    $form['enable_notifications'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Email Notifications'),
      '#description' => $this->t('Send email notifications when events are created or published.'),
      '#default_value' => $config->get('enable_notifications') ?? TRUE,
    ];

    $form['notification_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Notification Types'),
      '#description' => $this->t('Select which events should trigger notifications.'),
      '#options' => [
        'created' => $this->t('Event Created'),
        'published' => $this->t('Event Published'),
        'updated' => $this->t('Event Updated'),
      ],
      '#default_value' => $config->get('notification_types') ?? ['created', 'published'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('event_notifier.settings')
      ->set('notification_email', $form_state->getValue('notification_email'))
      ->set('enable_notifications', $form_state->getValue('enable_notifications'))
      ->set('notification_types', $form_state->getValue('notification_types'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
