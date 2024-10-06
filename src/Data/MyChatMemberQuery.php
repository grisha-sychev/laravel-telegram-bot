<?php

namespace Tgb\Data;

class MyChatMemberQuery
{
    private $updateId;
    private $myChatMember;
    private $chat;
    private $from;
    private $date;
    private $oldChatMember;
    private $newChatMember;
    private $chatId;
    private $chatTitle;
    private $chatUsername;
    private $chatType;
    private $fromId;
    private $fromIsBot;
    private $fromFirstName;
    private $fromLastName;
    private $fromLanguageCode;
    private $fromIsPremium;
    private $oldChatMemberUserId;
    private $oldChatMemberUserIsBot;
    private $oldChatMemberUserFirstName;
    private $oldChatMemberUserLastName;
    private $oldChatMemberUserLanguageCode;
    private $oldChatMemberUserIsPremium;
    private $oldChatMemberStatus;
    private $oldChatMemberCanBeEdited;
    private $oldChatMemberCanManageChat;
    private $oldChatMemberCanChangeInfo;
    private $oldChatMemberCanPostMessages;
    private $oldChatMemberCanEditMessages;
    private $oldChatMemberCanDeleteMessages;
    private $oldChatMemberCanInviteUsers;
    private $oldChatMemberCanRestrictMembers;
    private $oldChatMemberCanPromoteMembers;
    private $oldChatMemberCanManageVideoChats;
    private $oldChatMemberCanPostStories;
    private $oldChatMemberCanEditStories;
    private $oldChatMemberCanDeleteStories;
    private $oldChatMemberIsAnonymous;
    private $oldChatMemberCanManageVoiceChats;
    private $newChatMemberUserId;
    private $newChatMemberUserIsBot;
    private $newChatMemberUserFirstName;
    private $newChatMemberUserLastName;
    private $newChatMemberUserLanguageCode;
    private $newChatMemberUserIsPremium;
    private $newChatMemberStatus;
    private $newChatMemberCanBeEdited;
    private $newChatMemberCanManageChat;
    private $newChatMemberCanChangeInfo;
    private $newChatMemberCanPostMessages;
    private $newChatMemberCanEditMessages;
    private $newChatMemberCanDeleteMessages;
    private $newChatMemberCanInviteUsers;
    private $newChatMemberCanRestrictMembers;
    private $newChatMemberCanPromoteMembers;
    private $newChatMemberCanManageVideoChats;
    private $newChatMemberCanPostStories;
    private $newChatMemberCanEditStories;
    private $newChatMemberCanDeleteStories;
    private $newChatMemberIsAnonymous;
    private $newChatMemberCanManageVoiceChats;

    public function __construct(array $data)
    {
        $this->updateId = $data['update_id'] ?? null;
        $this->myChatMember = $data['my_chat_member'] ?? null;
        $this->date = $this->myChatMember['date'] ?? null;
        $this->chatId = $this->myChatMember['chat']['id'] ?? null;
        $this->chatTitle = $this->myChatMember['chat']['title'] ?? null;
        $this->chatUsername = $this->myChatMember['chat']['username'] ?? null;
        $this->chatType = $this->myChatMember['chat']['type'] ?? null;
        $this->fromId = $this->myChatMember['from']['id'] ?? null;
        $this->fromIsBot = $this->myChatMember['from']['is_bot'] ?? null;
        $this->fromFirstName = $this->myChatMember['from']['first_name'] ?? null;
        $this->fromLastName = $this->myChatMember['from']['last_name'] ?? null;
        $this->fromLanguageCode = $this->myChatMember['from']['language_code'] ?? null;
        $this->fromIsPremium = $this->myChatMember['from']['is_premium'] ?? null;
        $this->oldChatMemberUserId = $this->myChatMember['old_chat_member']['user']['id'] ?? null;
        $this->oldChatMemberUserIsBot = $this->myChatMember['old_chat_member']['user']['is_bot'] ?? null;
        $this->oldChatMemberUserFirstName = $this->myChatMember['old_chat_member']['user']['first_name'] ?? null;
        $this->oldChatMemberUserLastName = $this->myChatMember['old_chat_member']['user']['last_name'] ?? null;
        $this->oldChatMemberUserLanguageCode = $this->myChatMember['old_chat_member']['user']['language_code'] ?? null;
        $this->oldChatMemberUserIsPremium = $this->myChatMember['old_chat_member']['user']['is_premium'] ?? null;
        $this->oldChatMemberStatus = $this->myChatMember['old_chat_member']['status'] ?? null;
        $this->oldChatMemberCanBeEdited = $this->myChatMember['old_chat_member']['can_be_edited'] ?? null;
        $this->oldChatMemberCanManageChat = $this->myChatMember['old_chat_member']['can_manage_chat'] ?? null;
        $this->oldChatMemberCanChangeInfo = $this->myChatMember['old_chat_member']['can_change_info'] ?? null;
        $this->oldChatMemberCanPostMessages = $this->myChatMember['old_chat_member']['can_post_messages'] ?? null;
        $this->oldChatMemberCanEditMessages = $this->myChatMember['old_chat_member']['can_edit_messages'] ?? null;
        $this->oldChatMemberCanDeleteMessages = $this->myChatMember['old_chat_member']['can_delete_messages'] ?? null;
        $this->oldChatMemberCanInviteUsers = $this->myChatMember['old_chat_member']['can_invite_users'] ?? null;
        $this->oldChatMemberCanRestrictMembers = $this->myChatMember['old_chat_member']['can_restrict_members'] ?? null;
        $this->oldChatMemberCanPromoteMembers = $this->myChatMember['old_chat_member']['can_promote_members'] ?? null;
        $this->oldChatMemberCanManageVideoChats = $this->myChatMember['old_chat_member']['can_manage_video_chats'] ?? null;
        $this->oldChatMemberCanPostStories = $this->myChatMember['old_chat_member']['can_post_stories'] ?? null;
        $this->oldChatMemberCanEditStories = $this->myChatMember['old_chat_member']['can_edit_stories'] ?? null;
        $this->oldChatMemberCanDeleteStories = $this->myChatMember['old_chat_member']['can_delete_stories'] ?? null;
        $this->oldChatMemberIsAnonymous = $this->myChatMember['old_chat_member']['is_anonymous'] ?? null;
        $this->oldChatMemberCanManageVoiceChats = $this->myChatMember['old_chat_member']['can_manage_voice_chats'] ?? null;
        $this->newChatMemberUserId = $this->myChatMember['new_chat_member']['user']['id'] ?? null;
        $this->newChatMemberUserIsBot = $this->myChatMember['new_chat_member']['user']['is_bot'] ?? null;
        $this->newChatMemberUserFirstName = $this->myChatMember['new_chat_member']['user']['first_name'] ?? null;
        $this->newChatMemberUserLastName = $this->myChatMember['new_chat_member']['user']['last_name'] ?? null;
        $this->newChatMemberUserLanguageCode = $this->myChatMember['new_chat_member']['user']['language_code'] ?? null;
        $this->newChatMemberUserIsPremium = $this->myChatMember['new_chat_member']['user']['is_premium'] ?? null;
        $this->newChatMemberStatus = $this->myChatMember['new_chat_member']['status'] ?? null;
        $this->newChatMemberCanBeEdited = $this->myChatMember['new_chat_member']['can_be_edited'] ?? null;
        $this->newChatMemberCanManageChat = $this->myChatMember['new_chat_member']['can_manage_chat'] ?? null;
        $this->newChatMemberCanChangeInfo = $this->myChatMember['new_chat_member']['can_change_info'] ?? null;
        $this->newChatMemberCanPostMessages = $this->myChatMember['new_chat_member']['can_post_messages'] ?? null;
        $this->newChatMemberCanEditMessages = $this->myChatMember['new_chat_member']['can_edit_messages'] ?? null;
        $this->newChatMemberCanDeleteMessages = $this->myChatMember['new_chat_member']['can_delete_messages'] ?? null;
        $this->newChatMemberCanInviteUsers = $this->myChatMember['new_chat_member']['can_invite_users'] ?? null;
        $this->newChatMemberCanRestrictMembers = $this->myChatMember['new_chat_member']['can_restrict_members'] ?? null;
        $this->newChatMemberCanPromoteMembers = $this->myChatMember['new_chat_member']['can_promote_members'] ?? null;
        $this->newChatMemberCanManageVideoChats = $this->myChatMember['new_chat_member']['can_manage_video_chats'] ?? null;
        $this->newChatMemberCanPostStories = $this->myChatMember['new_chat_member']['can_post_stories'] ?? null;
        $this->newChatMemberCanEditStories = $this->myChatMember['new_chat_member']['can_edit_stories'] ?? null;
        $this->newChatMemberCanDeleteStories = $this->myChatMember['new_chat_member']['can_delete_stories'] ?? null;
        $this->newChatMemberIsAnonymous = $this->myChatMember['new_chat_member']['is_anonymous'] ?? null;
        $this->newChatMemberCanManageVoiceChats = $this->myChatMember['new_chat_member']['can_manage_voice_chats'] ?? null;
    }

    public function getUpdateId()
    {
        return $this->updateId;
    }

    public function getMyChatMember()
    {
        return $this->myChatMember;
    }

    public function getChat()
    {
        return $this->chat;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getOldChatMember()
    {
        return $this->oldChatMember;
    }

    public function getNewChatMember()
    {
        return $this->newChatMember;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function getChatTitle()
    {
        return $this->chatTitle;
    }

    public function getChatUsername()
    {
        return $this->chatUsername;
    }

    public function getChatType()
    {
        return $this->chatType;
    }

    public function getFromId()
    {
        return $this->fromId;
    }

    public function getFromIsBot()
    {
        return $this->fromIsBot;
    }

    public function getFromFirstName()
    {
        return $this->fromFirstName;
    }

    public function getFromLastName()
    {
        return $this->fromLastName;
    }

    public function getFromLanguageCode()
    {
        return $this->fromLanguageCode;
    }

    public function getFromIsPremium()
    {
        return $this->fromIsPremium;
    }

    public function getOldChatMemberUserId()
    {
        return $this->oldChatMemberUserId;
    }

    public function getOldChatMemberUserIsBot()
    {
        return $this->oldChatMemberUserIsBot;
    }

    public function getOldChatMemberUserFirstName()
    {
        return $this->oldChatMemberUserFirstName;
    }

    public function getOldChatMemberUserLastName()
    {
        return $this->oldChatMemberUserLastName;
    }

    public function getOldChatMemberUserLanguageCode()
    {
        return $this->oldChatMemberUserLanguageCode;
    }

    public function getOldChatMemberUserIsPremium()
    {
        return $this->oldChatMemberUserIsPremium;
    }

    public function getOldChatMemberStatus()
    {
        return $this->oldChatMemberStatus;
    }

    public function getOldChatMemberCanBeEdited()
    {
        return $this->oldChatMemberCanBeEdited;
    }

    public function getOldChatMemberCanManageChat()
    {
        return $this->oldChatMemberCanManageChat;
    }

    public function getOldChatMemberCanChangeInfo()
    {
        return $this->oldChatMemberCanChangeInfo;
    }

    public function getOldChatMemberCanPostMessages()
    {
        return $this->oldChatMemberCanPostMessages;
    }

    public function getOldChatMemberCanEditMessages()
    {
        return $this->oldChatMemberCanEditMessages;
    }

    public function getOldChatMemberCanDeleteMessages()
    {
        return $this->oldChatMemberCanDeleteMessages;
    }

    public function getOldChatMemberCanInviteUsers()
    {
        return $this->oldChatMemberCanInviteUsers;
    }

    public function getOldChatMemberCanRestrictMembers()
    {
        return $this->oldChatMemberCanRestrictMembers;
    }

    public function getOldChatMemberCanPromoteMembers()
    {
        return $this->oldChatMemberCanPromoteMembers;
    }

    public function getOldChatMemberCanManageVideoChats()
    {
        return $this->oldChatMemberCanManageVideoChats;
    }

    public function getOldChatMemberCanPostStories()
    {
        return $this->oldChatMemberCanPostStories;
    }

    public function getOldChatMemberCanEditStories()
    {
        return $this->oldChatMemberCanEditStories;
    }

    public function getOldChatMemberCanDeleteStories()
    {
        return $this->oldChatMemberCanDeleteStories;
    }

    public function getOldChatMemberIsAnonymous()
    {
        return $this->oldChatMemberIsAnonymous;
    }

    public function getOldChatMemberCanManageVoiceChats()
    {
        return $this->oldChatMemberCanManageVoiceChats;
    }

    public function getNewChatMemberUserId()
    {
        return $this->newChatMemberUserId;
    }

    public function getNewChatMemberUserIsBot()
    {
        return $this->newChatMemberUserIsBot;
    }

    public function getNewChatMemberUserFirstName()
    {
        return $this->newChatMemberUserFirstName;
    }

    public function getNewChatMemberUserLastName()
    {
        return $this->newChatMemberUserLastName;
    }

    public function getNewChatMemberUserLanguageCode()
    {
        return $this->newChatMemberUserLanguageCode;
    }

    public function getNewChatMemberUserIsPremium()
    {
        return $this->newChatMemberUserIsPremium;
    }

    public function getNewChatMemberStatus()
    {
        return $this->newChatMemberStatus;
    }

    public function getNewChatMemberCanBeEdited()
    {
        return $this->newChatMemberCanBeEdited;
    }

    public function getNewChatMemberCanManageChat()
    {
        return $this->newChatMemberCanManageChat;
    }

    public function getNewChatMemberCanChangeInfo()
    {
        return $this->newChatMemberCanChangeInfo;
    }

    public function getNewChatMemberCanPostMessages()
    {
        return $this->newChatMemberCanPostMessages;
    }

    public function getNewChatMemberCanEditMessages()
    {
        return $this->newChatMemberCanEditMessages;
    }

    public function getNewChatMemberCanDeleteMessages()
    {
        return $this->newChatMemberCanDeleteMessages;
    }

    public function getNewChatMemberCanInviteUsers()
    {
        return $this->newChatMemberCanInviteUsers;
    }

    public function getNewChatMemberCanRestrictMembers()
    {
        return $this->newChatMemberCanRestrictMembers;
    }

    public function getNewChatMemberCanPromoteMembers()
    {
        return $this->newChatMemberCanPromoteMembers;
    }

    public function getNewChatMemberCanManageVideoChats()
    {
        return $this->newChatMemberCanManageVideoChats;
    }

    public function getNewChatMemberCanPostStories()
    {
        return $this->newChatMemberCanPostStories;
    }

    public function getNewChatMemberCanEditStories()
    {
        return $this->newChatMemberCanEditStories;
    }

    public function getNewChatMemberCanDeleteStories()
    {
        return $this->newChatMemberCanDeleteStories;
    }

    public function getNewChatMemberIsAnonymous()
    {
        return $this->newChatMemberIsAnonymous;
    }

    public function getNewChatMemberCanManageVoiceChats()
    {
        return $this->newChatMemberCanManageVoiceChats;
    }

}
