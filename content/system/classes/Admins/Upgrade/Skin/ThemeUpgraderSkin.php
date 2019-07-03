<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
namespace Ecjia\System\Admins\Upgrade\Skin;

use Ecjia\System\Admins\Upgrade\UpgraderSkin;
use RC_Format;

/**
 * Theme Upgrader Skin for ECJia Theme Upgrades.
 *
 * @package ECJia
 * @subpackage Upgrader
 * @since 2.8.0
 */
class ThemeUpgraderSkin extends UpgraderSkin
{
    
    protected $theme = '';
    
    public function __construct($args = array())
    {
        $defaults = array(
            'url' => '',
            'theme' => '',
            'nonce' => '',
            'title' => __('Update Theme')
        );
        $args = rc_parse_args($args, $defaults);
    
        $this->theme = $args['theme'];
    
        parent::__construct($args);
    }
    
    public function after()
    {
        $this->decrement_update_count( 'theme' );
    
        $update_actions = array();
        if ( ! empty( $this->upgrader->result['destination_name'] ) && $theme_info = $this->upgrader->theme_info() ) {
            $name       = $theme_info->display('Name');
            $stylesheet = $this->upgrader->result['destination_name'];
            $template   = $theme_info->get_template();
    
            $preview_link = \RC_Uri::add_query_arg( array(
                'preview'    => 1,
                'template'   => urlencode( $template ),
                'stylesheet' => urlencode( $stylesheet ),
            ), trailingslashit( home_url() ) );
    
            $activate_link = \RC_Uri::add_query_arg( array(
                'action'     => 'activate',
                'template'   => urlencode( $template ),
                'stylesheet' => urlencode( $stylesheet ),
            ), admin_url('themes.php') );
            $activate_link = wp_nonce_url( $activate_link, 'switch-theme_' . $stylesheet );
    
            if ( get_stylesheet() == $stylesheet ) {
                if ( current_user_can( 'edit_theme_options' ) )
                    $update_actions['preview']  = '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Customize &#8220;%s&#8221;'), $name ) ) . '">' . __('Customize') . '</a>';
            } elseif ( current_user_can( 'switch_themes' ) ) {
                $update_actions['preview']  = '<a href="' . RC_Format::esc_url( $preview_link ) . '" class="hide-if-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Preview') . '</a>';
                $update_actions['preview'] .= '<a href="' . wp_customize_url( $stylesheet ) . '" class="hide-if-no-customize load-customize" title="' . esc_attr( sprintf( __('Preview &#8220;%s&#8221;'), $name ) ) . '">' . __('Live Preview') . '</a>';
                $update_actions['activate'] = '<a href="' . RC_Format::esc_url( $activate_link ) . '" class="activatelink" title="' . esc_attr( sprintf( __('Activate &#8220;%s&#8221;'), $name ) ) . '">' . __('Activate') . '</a>';
            }
    
            if ( ! $this->result || is_ecjia_error( $this->result ) || is_network_admin() )
                unset( $update_actions['preview'], $update_actions['activate'] );
        }
    
        $update_actions['themes_page'] = '<a href="' . self_admin_url('themes.php') . '" title="' . esc_attr__('Return to Themes page') . '" target="_parent">' . __('Return to Themes page') . '</a>';
    
        /**
         * Filter the list of action links available following a single theme update.
         *
         * @since 2.8.0
         *
         * @param array  $update_actions Array of theme action links.
         * @param string $theme          Theme directory name.
         */
        $update_actions = RC_Hook::apply_filters( 'update_theme_complete_actions', $update_actions, $this->theme );
    
        if ( ! empty($update_actions) )
        {
            $this->feedback(implode(' | ', (array)$update_actions));
        }
    }
    
}

// end