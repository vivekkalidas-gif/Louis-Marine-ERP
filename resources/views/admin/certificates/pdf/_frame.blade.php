{{-- Fixed page furniture (repeated on every page): frame, bands, header, footer.
     Params: $certificate, $brand, $sig, $qrCodeBase64 (optional), $continuation (bool). --}}
<div class="topband"></div>
<div class="topband-accent"></div>
<div class="frame-outer"></div>
<div class="frame-inner"></div>
<div class="bottomband"></div>
@if($brand['logo'])
    <img class="watermark" src="{{ $brand['logo'] }}" alt="">
@endif

<div class="hdr">
    <table>
        <tr>
            @if($brand['logo'])
                <td style="width: 48mm;"><img class="hdr-logo" src="{{ $brand['logo'] }}" alt=""></td>
            @endif
            <td>
                <div class="co-name">{{ $brand['name'] }}</div>
                @if($brand['address'])<div class="co-line">{{ $brand['address'] }}</div>@endif
                <div class="co-line">
                    {{ collect([$brand['phone'] ? 'Tel ' . $brand['phone'] : null, $brand['email'], $brand['website']])->filter()->implode('  ·  ') }}
                </div>
            </td>
            <td style="width: 47mm; padding-left: 3mm;">
                <div class="certno-box">
                    <div class="certno-label">{{ $continuation ? 'CONTINUATION OF' : 'CERTIFICATE No.' }}</div>
                    <div class="certno">{{ $certificate->certificate_number ?: 'DRAFT' }}</div>
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="hdr-rule"></div>

<div class="ftr">
    <table>
        <tr>
            <td style="width: 37%; text-align: center;">
                @if($img = $sig($certificate->assignedVerifier))
                    <img class="sig-img" src="{{ $img }}" alt="">
                @else
                    <div class="sig-space"></div>
                @endif
                <div class="sig-line">
                    <div class="sig-name">{{ $certificate->assignedVerifier->name ?? ' ' }}</div>
                    <div class="sig-role">Verifying Engineer</div>
                </div>
            </td>
            <td style="width: 26%; text-align: center;">
                @if(! empty($qrCodeBase64))
                    <img class="qr" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="">
                    <div class="qr-label">SCAN TO VERIFY</div>
                @endif
            </td>
            <td style="width: 37%; text-align: center;">
                @if($img = $sig($certificate->issuedBy))
                    <img class="sig-img" src="{{ $img }}" alt="">
                @else
                    <div class="sig-space"></div>
                @endif
                <div class="sig-line">
                    <div class="sig-name">{{ $certificate->issuedBy->name ?? ' ' }}</div>
                    <div class="sig-role">Authorised Signatory</div>
                </div>
            </td>
        </tr>
    </table>
    <div class="fineprint">
        Issued electronically by {{ rtrim($brand['name'], '. ') }}@if($certificate->certificate_number) · Verify by scanning the QR code or at {{ preg_replace('#^https?://#', '', route('certificates.verify-public', $certificate->certificate_number)) }}@endif
    </div>
</div>
