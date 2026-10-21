<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'action', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'ip', 'user_agent'])]
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionLabel(): string
    {
        return self::labelFor($this->action);
    }

    public static function labelFor(string $action): string
    {
        $labels = [
            'auth.login' => 'लॉगिन',
            'auth.logout' => 'लॉगआउट',
            'auth.login_failed' => 'असफल लॉगिन',
            'settings.updated' => 'सेटिंग्स अपडेट',
            'membership.registered' => 'नई सदस्यता',
            'membership.renewal_initiated' => 'नवीनीकरण प्रारंभ',
            'member.created' => 'सदस्य जोड़ा',
            'member.updated' => 'सदस्य अपडेट',
            'member.status_updated' => 'सदस्य स्थिति बदली',
            'member.payment_recorded' => 'सदस्य भुगतान दर्ज',
            'member.archived' => 'सदस्य अस्वीकृत',
            'payment.paid' => 'भुगतान सफल',
            'payment.verified' => 'भुगतान सत्यापित',
            'payment.refunded' => 'भुगतान वापसी',
            'payment.utr_submitted' => 'UTR दर्ज',
            'payment.cash_requested' => 'नकद भुगतान अनुरोध',
            'payment.receipt_uploaded' => 'भुगतान रसीद अपलोड',
            'payment.cashfree_return' => 'Cashfree भुगतान',
            'payment.cashfree_webhook' => 'Cashfree वेबहुक',
            'grievance.created' => 'शिकायत दर्ज',
            'grievance.updated' => 'शिकायत अपडेट',
            'grievance.document_uploaded' => 'शिकायत दस्तावेज़ अपलोड',
            'grievance.document_deleted' => 'शिकायत दस्तावेज़ हटाया',
            'contact_message.received' => 'संपर्क संदेश प्राप्त',
            'contact_message.read' => 'संपर्क संदेश पढ़ा',
            'contact_message.deleted' => 'संपर्क संदेश हटाया',
            'news.created' => 'समाचार जोड़ा',
            'news.updated' => 'समाचार अपडेट',
            'news.deleted' => 'समाचार हटाया',
            'news_category.created' => 'समाचार श्रेणी जोड़ी',
            'news_category.updated' => 'समाचार श्रेणी अपडेट',
            'news_category.deleted' => 'समाचार श्रेणी हटाई',
            'page.updated' => 'पृष्ठ अपडेट',
            'menu.created' => 'मेनू जोड़ा',
            'menu.updated' => 'मेनू अपडेट',
            'menu.deleted' => 'मेनू हटाया',
            'banner.created' => 'बैनर जोड़ा',
            'banner.updated' => 'बैनर अपडेट',
            'banner.deleted' => 'बैनर हटाया',
            'action_card.created' => 'एक्शन कार्ड जोड़ा',
            'action_card.updated' => 'एक्शन कार्ड अपडेट',
            'action_card.deleted' => 'एक्शन कार्ड हटाया',
            'mission_card.created' => 'मिशन कार्ड जोड़ा',
            'mission_card.updated' => 'मिशन कार्ड अपडेट',
            'mission_card.deleted' => 'मिशन कार्ड हटाया',
            'gallery.created' => 'गैलरी आइटम जोड़ा',
            'gallery.updated' => 'गैलरी आइटम अपडेट',
            'gallery.deleted' => 'गैलरी आइटम हटाया',
            'notice.created' => 'सूचना जोड़ी',
            'notice.updated' => 'सूचना अपडेट',
            'notice.deleted' => 'सूचना हटाई',
            'team_member.created' => 'पदाधिकारी जोड़ा',
            'team_member.updated' => 'पदाधिकारी अपडेट',
            'team_member.deleted' => 'पदाधिकारी हटाया',
            'branch.created' => 'शाखा जोड़ी',
            'branch.updated' => 'शाखा अपडेट',
            'branch.deleted' => 'शाखा हटाई',
            'worker_category.created' => 'कार्यक्षेत्र जोड़ा',
            'worker_category.updated' => 'कार्यक्षेत्र अपडेट',
            'worker_category.deleted' => 'कार्यक्षेत्र हटाया',
            'admin_user.created' => 'प्रशासक जोड़ा',
            'admin_user.updated' => 'प्रशासक अपडेट',
            'admin_user.deleted' => 'प्रशासक हटाया',
            'admin.profile_updated' => 'प्रोफ़ाइल अपडेट',
            'admin.password_changed' => 'पासवर्ड बदला',
            'translation.updated' => 'अनुवाद अपडेट',
            'translation.reviewed' => 'अनुवाद समीक्षित',
        ];

        return $labels[$action] ?? $action;
    }

    public function targetLabel(): ?string
    {
        if (! $this->auditable_type) {
            return null;
        }

        $labels = [
            Member::class => 'सदस्य',
            Grievance::class => 'शिकायत',
            MembershipPayment::class => 'भुगतान',
            News::class => 'समाचार',
            NewsCategory::class => 'समाचार श्रेणी',
            Page::class => 'पृष्ठ',
            MenuItem::class => 'मेनू',
            Banner::class => 'बैनर',
            ActionCard::class => 'एक्शन कार्ड',
            MissionCard::class => 'मिशन कार्ड',
            Gallery::class => 'गैलरी',
            Notice::class => 'सूचना',
            TeamMember::class => 'पदाधिकारी',
            Branch::class => 'शाखा',
            WorkerCategory::class => 'कार्यक्षेत्र',
            User::class => 'प्रशासक',
            ContactMessage::class => 'संपर्क संदेश',
            Translation::class => 'अनुवाद',
            GrievanceDocument::class => 'शिकायत दस्तावेज़',
        ];

        return ($labels[$this->auditable_type] ?? class_basename($this->auditable_type)).' #'.$this->auditable_id;
    }
}
