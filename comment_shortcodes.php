<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2012 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
 */

if (!defined('e107_INIT')) { exit; }

e107::coreLan('comment');

class comment_shortcodes extends e_shortcode
{
    var $var;

    function sc_subject_input($parm = null)
    {
        $tp = e107::getParser();
        $pref = e107::getPref();
        $form = e107::getForm();

        if (!empty($pref['nested_comments'])) {
            $options = array(
                'class'       => 'comment subject-input form-control',
                'placeholder' => COMLAN_324,
                'size'        => 61,
            );

            $text = '<div class="form-group">';
            $text .= $form->text('subject', $tp->toForm(varset($this->var['subject'])), 100, $options);
            $text .= '</div>';

            return $text;
        }
    }

    function sc_subject($parm = '')
    {
        $tp = e107::getParser();
        $pref = e107::getPref();

        global $SUBJECT, $NEWIMAGE;

        if (!empty($pref['nested_comments'])) {
            $SUBJECT = $NEWIMAGE . " " . (empty($this->var['comment_subject']) ? $SUBJECT : $tp->toHTML($this->var['comment_subject'], TRUE));
        } else {
            $SUBJECT = '';
        }

        return trim($SUBJECT);
    }

    function sc_username($parm = null)
    {
        global $USERNAME;
        if (isset($this->var['comment_author_id']) && $this->var['comment_author_id']) {
            $USERNAME = $parm == 'raw' ? $this->var['comment_author_name'] : "<a href='" . e107::getUrl()->create('user/profile/view', array('id' => $this->var['comment_author_id'], 'name' => $this->var['comment_author_name'])) . "'>" . $this->var['comment_author_name'] . "</a>\n";
        } else {
            $this->var['user_id'] = 0;
            $USERNAME = preg_replace("/[0-9]+\./", '', vartrue($this->var['comment_author_name']));
            $USERNAME = str_replace("Anonymous", LAN_ANONYMOUS, $USERNAME);
        }
        return $USERNAME;
    }

    function sc_comment_timedate($parm = null)
    {
        if ($parm == 'relative') {
            return e107::getDate()->computeLapse($this->var['comment_datestamp'], time(), false, false, 'short');
        }

        return e107::getDate()->convert_date(varset($this->var['comment_datestamp'], 0), "short");
    }

    function sc_comment_reply($parm = null)
    {
        global $REPLY, $action, $table, $id, $thisaction, $thistable, $thisid;

        $pref = e107::getPref();
        $REPLY = '';

        // ✅ Check if nested comments are enabled
        if (empty($pref['nested_comments'])) {
            return ''; // 🛑 Do not show reply button at all
        }

        // ✅ Check user permissions
        if (USERID || vartrue($pref['anon_post']) == 1)
        {
            // ✅ Check if comment is open/unlocked
            if (!empty($this->var['comment_lock']) && $this->var['comment_lock'] == "1") {
                return '';
            }

            // ✅ Check if comment is not blocked
            if (!empty($this->var['comment_blocked']) && $this->var['comment_blocked'] >= 1) {
                return '';
            }

            // ✅ Fallback if $thisaction isn't defined
            if (empty($thisaction)) {
                $thisaction = 'comment';
            }

            // ✅ Show only if in comment view mode
            if ($thisaction === 'comment')
            {
                $REPLY = "<a id='e-comment-reply-".$this->var['comment_id']."' 
                    class='e-comment-reply btn btn-default btn-secondary btn-sm btn-mini btn-xs' 
                    data-type='".$this->var['comment_type']."' 
                    data-target='".e_HTTP."comment.php' 
                    href='".e_HTTP."comment.php?reply.".$thistable.".".$this->var['comment_id'].".".$thisid."'>".COMLAN_326."</a>";
            }
        }

        return $REPLY;
    }

    function sc_comment_avatar($parm = null)
    {
        $tp = e107::getParser();

        // Posting a new comment (check that it is not an existing comment by anonymous user) - #3813 & 3829
        // https://github.com/e107inc/e107/issues/4217
        if (!isset($this->var['comment_author_id']) && USERID) // assumes we are writing a new comment, not displaying an existing one.
        {
            $userdata = e107::user(USERID);
            $this->var = array_merge($this->var, $userdata);
        }

        $text = $tp->toAvatar($this->var, $parm);

        $text .= '<div class="field-help" style="display:none;">';
        $text .= '<div class="left">';
        $text .= '<h2>' . $this->sc_username() . '</h2>';
        $text .= $this->sc_joined() . '<br />' . $this->sc_comments() . '<br />' . $this->sc_rating() . $this->sc_location();
        $text .= '</div>';
        $text .= '</div>';

        return $text;
    }

    function sc_comments($parm = null)
    {
        global $COMMENTS;
        return (!empty($this->var['user_id']) ? LAN_COMMENTS . ": " . varset($this->var['user_comments']) : COMLAN_194) . "<br />";
    }

    function sc_joined($parm = null)
    {
        global $JOINED;
        $JOINED = '';
        if (!empty($this->var['user_id']) && empty($this->var['user_admin'])) {
            $joined = varset($this->var['user_join'], 0);
            $date = e107::getDate()->convert_date($joined, "short");
            $JOINED = ($this->var['user_join'] ? COMLAN_145 . " " . $date : '');
        }
        return $JOINED;
    }

    function sc_comment_itemid($parm = null) // for ajax item id.
    {
        return 'comment-' . intval($this->var['comment_id']);
    }

    function sc_comment_moderate($parm = null)
    {
        if (!getperms('0') && !getperms("B")) {
            return null;
        }

        $text = "<a href='#' data-target='" . e_HTTP . "comment.php' id='e-comment-delete-" . $this->var['comment_id'] . "'  data-type='" . $this->var['comment_type'] . "' data-itemid='" . $this->var['comment_item_id'] . "' class='e-comment-delete btn btn-default btn-secondary btn-sm btn-mini btn-xs'>" . LAN_DELETE . "</a> ";

        if ($this->var['comment_blocked'] == 2) // pending approval. 
        {
            $text .= "<a href='#' data-target='" . e_HTTP . "comment.php' id='e-comment-approve-" . $this->var['comment_id'] . "' class='e-comment-approve btn btn-default btn-secondary btn-sm btn-mini btn-xs'>" . COMLAN_404 . "</a> ";
        }
        return $text;
    }

    function sc_comment_button($parm = null)
    {
        $pref = e107::getPref('comments_sort');
        $form = e107::getForm();

        if ($this->mode == 'edit') {
            $value = (varset($this->var['eaction']) == "edit" ? COMLAN_320 : COMLAN_9);
            $pid = ($this->var['action'] == 'reply') ? $this->var['pid'] : 0;

            $class = "e-comment-submit ";
            $class .= (!empty($parm['class'])) ? $parm['class'] : 'button btn btn-primary e-comment-submit pull-right float-end float-right';
            $options = array(
                'class'         => $class,
                'data-pid'      => $pid,
                'data-sort'     => $pref,
                'data-target'   => e_HTTP . 'comment.php',
                'data-container' => 'comments-container-' . $form->name2id($this->var['table']),
                'data-input'    => 'comment-input-' . $form->name2id($this->var['table'])
            );

            return $form->submit($this->var['action'] . 'submit', $value, $options);
        }
    }

    function sc_author_input($parm = null)
    {
        if ($this->mode == 'edit') {
            if (ANON == true && USER == false) // (anonymous comments - if allowed)
            {
                $form = e107::getForm();

                $inputclass = (!empty($parm['inputclass'])) ? $parm['inputclass'] : 'comment author form-control';
                $class = (!empty($parm['class'])) ? $parm['class'] : 'form-group';

                $options = array(
                    'class'       => $inputclass,
                    'placeholder' => COMLAN_16,
                    'size'        => 61,
                );

                // Prevent anon users changing names on the same session.
                if (vartrue($_SESSION['comment_author_name'])) {
                    $options['disabled'] = 'disabled';
                }

                $text = '<div class="' . $class . '">';
                $text .= $form->text('author_name', $_SESSION['comment_author_name'], 100, $options);
                $text .= '</div>';

                return $text;
            }
        }
    }

    function sc_comment_rate($parm = null)
    {
        if ($this->var['comment_blocked'] > 0 || varset($this->var['rating_enabled']) == false) {
            return null;
        }

        $curVal = array(
            'up'    => $this->var['rate_up'],
            'down'  => $this->var['rate_down'],
            'total' => $this->var['rate_votes']
        );

        return e107::getRate()->renderLike("comments", $this->var['comment_id'], $curVal);
    }

    function sc_comment_input($parm = null)
    {
        $inputclass = (!empty($parm['inputclass'])) ? $parm['inputclass'] : 'comment-input form-control';
        $class = (!empty($parm['class'])) ? $parm['class'] : 'form-group';
        $options = array(
            'class'       => $inputclass,
            'placeholder' => COMLAN_403,
            'id'          => 'comment-input-' . e107::getForm()->name2id($this->var['table'])
        );

        $text = '<div class="' . $class . '">';

        if ($parm == 'bbcode') {
            $text .= e107::getForm()->bbarea('comment', $this->var['comval'], 'comment', 'comment-' . $this->var['itemid'], 'large', $options);
        } else {
            $text .= e107::getForm()->textarea('comment', $this->var['comval'], 3, 80, $options);
        }

        $text .= '</div>';

        return $text;
    }

    function sc_comment($parm = null)
    {
        $tp = e107::getParser();
        if ($this->var['comment_blocked'] == 1) {
            return COMLAN_0;
        }

        return $tp->toHTML($this->var['comment_comment'], TRUE, FALSE, $this->var['user_id']);
    }

    function sc_comment_status($parm = null)
    {
        switch ($this->var['comment_blocked']) {
            case 2:
                $text = COMLAN_331;
                break;

            case 1:
                $text = COMLAN_0;
                break;

            default:
                return null;
        }

        return "<span id='comment-status-" . $this->var['comment_id'] . "'>" . $text . "</span>";
    }

    function sc_comment_edit($parm = null)
    {
        global $COMMENTEDIT, $comment_edit_query;
        $pref = e107::getPref();

        if ($pref['allowCommentEdit'] && USER && $this->var['user_id'] == USERID && ($this->var['comment_lock'] < 1)) {
            $adop_icon = (file_exists(THEME . "images/commentedit.png") ? "<img src='" . THEME_ABS . "images/commentedit.png' alt='" . COMLAN_318 . "' title='" . COMLAN_318 . "' class='icon' />" : LAN_EDIT);
            if (strpos(e_QUERY, "&") !== false) {
                return "<a data-target='" . e_HTTP . "comment.php' id='e-comment-edit-" . $this->var['comment_id'] . "' class='btn btn-default btn-secondary btn-sm btn-mini btn-xs e-comment-edit' href='" . e_SELF . "?" . e_QUERY . "&amp;comment=edit&amp;comment_id=" . $this->var['comment_id'] . "'>{$adop_icon}</a>";
            } else {
                return "<a data-target='" . e_HTTP . "comment.php' id='e-comment-edit-" . $this->var['comment_id'] . "' class='btn btn-default btn-secondary btn-sm btn-mini btn-xs e-comment-edit' href='" . SITEURL . "comment.php?" . $comment_edit_query . ".edit." . $this->var['comment_id'] . "#e-comment-form'>" . $adop_icon . "</a>";
            }
        } else {
            return "";
        }
    }

    function sc_rating($parm = null)
    {
        global $RATING;
        return $RATING;
    }

    function sc_ipaddress($parm = null)
    {
        return (ADMIN ? "<a href='" . SITEURL . "userposts.php?0.comments." . $this->var['user_id'] . "'>" . COMLAN_330 . " " . e107::getIPHandler()->ipDecode($this->var['comment_ip']) . "</a>" : "");
    }

    function sc_level($parm = null)
    {
    }

    function sc_location($parm = null)
    {
        $tp = e107::getParser();
        return (isset($this->var['user_location']) && $this->var['user_location'] ? COMLAN_313 . ": " . $tp->toHTML($this->var['user_location'], TRUE) : '');
    }

    function sc_signature($parm = null)
    {
        $tp = e107::getParser();
        $SIGNATURE = (isset($this->var['user_signature']) && $this->var['user_signature'] ? $tp->toHTML($this->var['user_signature'], true) : '');
        return $SIGNATURE;
    }

    function sc_comment_share($parm = null)
    {
        if (!$xup = e107::getUser()->getProviderName()) {
            return null;
        }

        list($prov, $id) = explode("_", $xup);
        $prov = strtolower($prov);

        if ($prov == 'facebook' || $prov == 'twitter') //TODO Get this working!
        {
            $text = e107::getForm()->hidden('comment_share', '');
            return $text;
        }
    }
}
