@extends('mail.minty')

@section('content')
    @include('beautymail::templates.minty.contentStart')
    <tr>
        <td width="100%" height="5"></td>
    </tr>
    <tr>
        <td class="title" style="font-size:1.1rem;">
            Enquiry
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Hi,
        </td>
    </tr>    
    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td class="paragraph">
            Welcome to Nikshy setu!
        </td>
    </tr>
    <tr>
        <td class="paragraph">
            Thank you for submitting your query with following details.
        </td>
    </tr>
    <tr>
        <td width="100%" height="25"></td>
    </tr>

    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Name
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{$name}}
            </div><br/>
        </td>
    </tr>

    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Cadre
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{ $enquiry->user->cadre && $enquiry->user->cadre->title ? $enquiry->user->cadre->title : '' }}
            </div><br/>
        </td>
    </tr>

    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                State
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{ $enquiry->user->state && $enquiry->user->state->title ? $enquiry->user->state->title : '' }}
            </div><br/>
        </td>
    </tr>
    @if($enquiry->user->district && $enquiry->user->district->title)
    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                District
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{ $enquiry->user->district && $enquiry->user->district->title ? $enquiry->user->district->title : '' }}
            </div><br/>
        </td>
    </tr>
    @endif
    @if($enquiry->user->block && $enquiry->user->block->title)
    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Block
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{ $enquiry->user->block && $enquiry->user->block->title ? $enquiry->user->block->title : '' }}
            </div><br/>
        </td>
    </tr>
    @endif
    @if($enquiry->user->health_facility && $enquiry->user->health_facility->health_facility_code)
    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Health Facility
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{ $enquiry->user->health_facility && $enquiry->user->health_facility->health_facility_code ? $enquiry->user->health_facility->health_facility_code : '' }}
            </div><br/>
        </td>
    </tr>
    @endif
    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Email
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{$email}}
            </div><br/>
        </td>
    </tr>

    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Subject
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{$subject}}
            </div><br/>
        </td>
    </tr>


    <tr>
        <td>
            <h3 style="color:#5F5F5F;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">
                Message
            </h3>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5F5F5F;line-height:135%;">
                {{$description}}
            </div><br/>
        </td>
    </tr>

    <tr>
        <td width="100%" height="10"></td>
    </tr>
    <tr>
        <td class="paragraph">
        Regards,
        </td>
    </tr>

    <tr>
        <td class="paragraph">
            Nikshy setu Team
        </td>
    </tr>
    <tr>
        <td width="100%" height="35"></td>
    </tr>
    @include('beautymail::templates.minty.contentEnd')
@stop
