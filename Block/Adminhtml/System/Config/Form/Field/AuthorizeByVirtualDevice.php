<?php
/**
 * Copyright © 2025 Alliance Dgtl. https://alb.ua/uk
 */

declare(strict_types=1);

namespace Alliance\AlliancePay\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class DisabledField.
 */
class AuthorizeByVirtualDevice extends Field
{
    private const BUTTON_LABEL = 'Authorize by virtual device';

    /**
     * @return $this|AuthorizeByVirtualDevice
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('Alliance_AlliancePay::system/config/auth_request_button.phtml');
        }

        return $this;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : self::BUTTON_LABEL;
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'ajax_url' => $this->_urlBuilder->getUrl('alliance_pay/system_config_authorize/authorize'),
            ]
        );

        return $this->_toHtml();
    }
}
