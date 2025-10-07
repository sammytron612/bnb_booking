# Payment Dispute Alert 🚨

**A payment dispute has been filed against one of your bookings.**

---

## Dispute Details

**Amount:** £{{ $dispute->amount_in_pounds }}
**Status:** {{ $dispute->friendly_status }}
**Reason:** {{ $dispute->friendly_reason }}
**Stripe Dispute ID:** `{{ $dispute->stripe_dispute_id }}`

@if($dispute->evidence_due_by)
**⏰ Evidence Due:** {{ $dispute->evidence_due_by->format('d/m/Y H:i') }}
**Days Remaining:** {{ $dispute->days_until_due }} days
@if($dispute->is_urgent)

**🚨 URGENT: Evidence due in {{ $dispute->days_until_due }} days or less!**
@endif
@endif

---

## Booking Information

**Guest:** {{ $guest }}
**Booking ID:** {{ $booking->id }}
**Check-in:** {{ $booking->checkin_date->format('d/m/Y') }}
**Check-out:** {{ $booking->checkout_date->format('d/m/Y') }}
**Total Amount:** £{{ number_format($booking->total_amount / 100, 2) }}

---

## Next Steps

1. **Review the dispute details** in your Stripe dashboard
2. **Gather evidence** (booking confirmation, communication, photos, etc.)
3. **Respond promptly** if evidence is required
4. **Contact guest** if appropriate to resolve directly

<x-mail::button :url="$stripeDisputeUrl" color="primary">
View in Stripe Dashboard
</x-mail::button>

---

### Tips for Dispute Response:

- **Booking confirmation** emails and receipts
- **Communication history** with the guest
- **Property photos** and descriptions
- **Proof of service delivery** (check-in confirmations, etc.)
- **Cancellation policy** evidence if applicable

---

*This dispute was automatically detected and logged in your system. Please respond promptly to protect your revenue.*

**Eileen's BnB Management System**
