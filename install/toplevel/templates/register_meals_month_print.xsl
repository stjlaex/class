<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several
         assessment grades, a second additional table displays (optional)
         short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="students">

  <div id='logo'>
        <img src="../images/schoollogo.png" width="100px"/>
  </div>

  <div class="studenthead">
        <table id="studentdata">
          <tr>
                <td>
                  <label>
                        Meals attendance
                  </label>
                  <xsl:value-of select="mealsattendance/formgroup/value/text()" />&#160;
                </td>
          </tr>
          <tr>
                <td>
                  <label>
                        Date:
                  </label>
                  <xsl:value-of select="mealsattendance/date/value/text()" />&#160;
                </td>
          </tr>
        </table>
  </div>

  <div class='spacer'>
        <xsl:text>&#160;</xsl:text>
  </div>

  <table class="grades" id="fullwidth">
        <tr>
          <th colspan="3" rowspan="2">
                Student
          </th>
          <xsl:for-each select="/students/dates/date[not(week=preceding::week)]">
                <xsl:variable name="weekno" select="week" />
                <th colspan="{count(/students/dates/date[week=$weekno])}">
                        <xsl:text>Week&#160;</xsl:text>
                        <xsl:value-of select="week" />
                </th>
          </xsl:for-each>
          <th rowspan="2">Total</th>
        </tr>
        <tr>
          <xsl:for-each select="/students/dates/date">
                <th>
                        <xsl:value-of select="day" />
                </th>
          </xsl:for-each>
        </tr>
        <xsl:apply-templates select="student" />
  </table>


  <xsl:if test="position()!=last()">
        <hr />
  </xsl:if>


</xsl:template>

<xsl:template match="student">
  <tr>
        <th>
          <xsl:value-of select="position()" />
        </th>
        <td style="text-align:left;">
          <xsl:value-of select="displayfullname/value/text()" />&#160;
        </td>
        <th>
          <xsl:value-of select="registrationgroup/value/text()" />
        </th>
        <xsl:variable name="attendances" select="attendances" />
        <xsl:for-each select="/students/dates/date">
                <xsl:variable name="date" select="value" />
                <th>
                <xsl:if test="$attendances/attendance[event/date/value=$date] and $attendances/attendance[event/date/value=$date]/status/value='p'">
                        /
                </xsl:if>
                </th>
        </xsl:for-each>
        <th>
                <xsl:value-of select="count($attendances/attendance[status/value='p'])" />
        </th>
  </tr>
</xsl:template>


</xsl:stylesheet>

