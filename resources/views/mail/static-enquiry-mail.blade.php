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
